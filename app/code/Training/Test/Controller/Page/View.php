<?php

namespace Training\Test\Controller\Page;

use Exception;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class View
 * @package Training\Test\Controller\Page
 */
class View extends \Magento\Cms\Controller\Page\View
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * View constructor.
     * @param Context $context
     * @param RequestInterface $request
     * @param PageHelper $pageHelper
     * @param ForwardFactory $resultForwardFactory
     * @param JsonFactory $resultJsonFactory
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        PageHelper $pageHelper,
        ForwardFactory $resultForwardFactory,
        JsonFactory $resultJsonFactory,
        PageRepositoryInterface $pageRepository
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->pageRepository = $pageRepository;
        parent::__construct($context, $request, $pageHelper, $resultForwardFactory);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $data = ['status' => 'success', 'message' => ''];

            $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
            $resultJson = $this->resultJsonFactory->create();
            try {
                $page = $this->pageRepository->getById($pageId);
                $data['title'] = $page->getTitle();
                $data['content'] = $page->getContent();
            } catch (NoSuchEntityException $e) {
                $data['status'] = 'error';
                $data['message'] = 'Not found';
            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = 'Something wrong';
            }
            $resultJson->setData($data);
            return $resultJson;
        }
        return parent::execute();
    }
}
