<?php

namespace Training\Test\App;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Request\ValidatorInterface as RequestValidator;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterListInterface;
use Magento\Framework\App\State;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Profiler;
use Psr\Log\LoggerInterface;

class FrontController extends \Magento\Framework\App\FrontController
{
    /**
     * @var \Magento\Framework\App\RouterListInterface
     */
    protected $routerList;
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $anotherLogger;


    /**
     * REDEFINED ALL PRIVATE PROPERTIES FROM NATIVE CONTROLLER
     */


    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestValidator
     */
    private $requestValidator;

    /**
     * @var MessageManager
     */
    private $messages;

    /**
     * @var bool
     */
    private $validatedRequest = false;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * FrontController constructor.
     * @param RouterListInterface $routerList
     * @param ResponseInterface $response
     * @param RequestValidator|null $requestValidator
     * @param MessageManager|null $messageManager
     * @param LoggerInterface|null $logger
     * @param State|null $appState
     * @param AreaList|null $areaList
     * @param ActionFlag|null $actionFlag
     * @param EventManagerInterface|null $eventManager
     * @param RequestInterface|null $request
     * @param LoggerInterface $anotherLogger
     */
    public function __construct(
        RouterListInterface $routerList,
        ResponseInterface $response,
        ?RequestValidator $requestValidator = null,
        ?MessageManager $messageManager = null,
        ?LoggerInterface $logger = null,
        ?State $appState = null,
        ?AreaList $areaList = null,
        ?ActionFlag $actionFlag = null,
        ?EventManagerInterface $eventManager = null,
        ?RequestInterface $request = null,
        LoggerInterface $anotherLogger
    ) {
        parent::__construct(
            $routerList,
            $response,
            $requestValidator,
            $messageManager,
            $logger,
            $appState,
            $areaList,
            $actionFlag,
            $eventManager,
            $request
        );

        /*not needed because we could use core native $_routerList (with underscore)*/
        $this->routerList = $routerList;
        $this->response = $response;
        /*Here used another logger because probably we planned logging in a specific place in a separate file for example*/
        $this->anotherLogger = $anotherLogger;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|ResultInterface|null
     * @throws LocalizedException
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        //here could be used $this->_routerList from native front controller instead of $this->routerList
        foreach ($this->routerList as $router) {
            $this->anotherLogger->info(get_class($router));
        }
        return parent::dispatch($request);
    }

    /**
     * REDEFINED (just copied) ALL PRIVATE METHODS FROM NATIVE CONTROLLER
     */

    /**
    * Process (validate and dispatch) the incoming request
    *
    * @param RequestInterface $request
    * @param ActionInterface $actionInstance
    * @return ResponseInterface|ResultInterface
    * @throws LocalizedException
    *
    * @throws NotFoundException
    */
    private function processRequest(
        RequestInterface $request,
        ActionInterface $actionInstance
    ) {
        $request->setDispatched(true);
        $this->response->setNoCacheHeaders();
        $result = null;

        //Validating a request only once.
        if (!$this->validatedRequest) {
            $area = $this->areaList->getArea($this->appState->getAreaCode());
            $area->load(Area::PART_DESIGN);
            $area->load(Area::PART_TRANSLATE);

            try {
                $this->requestValidator->validate($request, $actionInstance);
            } catch (InvalidRequestException $exception) {
                //Validation failed - processing validation results.
                $this->logger->debug(
                    sprintf('Request validation failed for action "%s"', get_class($actionInstance)),
                    ["exception" => $exception]
                );
                $result = $exception->getReplaceResult();
                if ($messages = $exception->getMessages()) {
                    foreach ($messages as $message) {
                        $this->messages->addErrorMessage($message);
                    }
                }
            }
            $this->validatedRequest = true;
        }

        // Validation did not produce a result to replace the action's.
        if (!$result) {
            $this->dispatchPreDispatchEvents($actionInstance, $request);
            $result = $this->getActionResponse($actionInstance, $request);
            if (!$this->isSetActionNoPostDispatchFlag()) {
                $this->dispatchPostDispatchEvents($actionInstance, $request);
            }
        }

        //handling redirect to 404
        if ($result instanceof NotFoundException) {
            throw $result;
        }
        return $result;
    }
    /**
     * Return the result of processed request
     *
     * There are 3 ways of handling requests:
     * - Result without dispatching event when FLAG_NO_DISPATCH is set, just return ResponseInterface
     * - Backwards-compatible way using `AbstractAction::dispatch` which is deprecated
     * - Correct way for handling requests with `ActionInterface::execute`
     *
     * @param ActionInterface $actionInstance
     * @param RequestInterface $request
     * @return ResponseInterface|ResultInterface
     * @throws NotFoundException
     */
    private function getActionResponse(ActionInterface $actionInstance, RequestInterface $request)
    {
        if ($this->actionFlag->get('', ActionInterface::FLAG_NO_DISPATCH)) {
            return $this->response;
        }

        if ($actionInstance instanceof AbstractAction) {
            return $actionInstance->dispatch($request);
        }

        return $actionInstance->execute();
    }

    /**
     * Check if action flags are set that would suppress the post dispatch events.
     *
     * @return bool
     */
    private function isSetActionNoPostDispatchFlag(): bool
    {
        return $this->actionFlag->get('', ActionInterface::FLAG_NO_DISPATCH)
            || $this->actionFlag->get('', ActionInterface::FLAG_NO_POST_DISPATCH);
    }

    /**
     * Dispatch the controller_action_predispatch events.
     *
     * @param ActionInterface $actionInstance
     * @param RequestInterface $request
     * @return void
     */
    private function dispatchPreDispatchEvents(ActionInterface $actionInstance, RequestInterface $request): void
    {
        $this->eventManager->dispatch('controller_action_predispatch', $this->getEventParameters($actionInstance));
        if ($this->request instanceof HttpRequest) {
            $this->eventManager->dispatch(
                'controller_action_predispatch_' . $request->getRouteName(),
                $this->getEventParameters($actionInstance)
            );
            $this->eventManager->dispatch(
                'controller_action_predispatch_' . $request->getFullActionName(),
                $this->getEventParameters($actionInstance)
            );
        }
    }

    /**
     * Dispatch the controller_action_postdispatch events.
     *
     * @param ActionInterface $actionInstance
     * @param RequestInterface $request
     * @return void
     */
    private function dispatchPostDispatchEvents(ActionInterface $actionInstance, RequestInterface $request): void
    {
        Profiler::start('postdispatch');
        if ($this->request instanceof HttpRequest) {
            $this->eventManager->dispatch(
                'controller_action_postdispatch_' . $request->getFullActionName(),
                $this->getEventParameters($actionInstance)
            );
            $this->eventManager->dispatch(
                'controller_action_postdispatch_' . $request->getRouteName(),
                $this->getEventParameters($actionInstance)
            );
        }
        $this->eventManager->dispatch('controller_action_postdispatch', $this->getEventParameters($actionInstance));
        Profiler::stop('postdispatch');
    }

    /**
     * Build the event parameter array
     *
     * @param ActionInterface $subject
     * @return array
     */
    private function getEventParameters(ActionInterface $subject): array
    {
        return ['controller_action' => $subject, 'request' => $this->request];
    }
}
