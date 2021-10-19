<?php

namespace Training\Feedback\Controller\Index;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\ResourceModel\Feedback;

class Save implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var FeedbackFactory
     */
    private $feedbackFactory;
    /**
     * @var Feedback
     */
    private $feedbackResource;
    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Save constructor.
     * @param FeedbackFactory $feedbackFactory
     * @param Feedback $feedbackResource
     * @param MessageManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param RequestInterface $request
     */
    public function __construct(
        FeedbackFactory $feedbackFactory,
        Feedback $feedbackResource,
        MessageManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        RequestInterface $request
    ) {
        $this->feedbackFactory = $feedbackFactory;
        $this->feedbackResource = $feedbackResource;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->request = $request;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultRedirectFactory->create();
        if ($post = $this->request->getPostValue()) {
            try {
                $this->validatePost($post);
                $feedback = $this->feedbackFactory->create();
                $feedback->setData($post);
                try {
                    $this->feedbackResource->save($feedback);
                    $this->messageManager->addSuccessMessage(
                        __('Thank you for your feedback.')
                    );
                } catch (AlreadyExistsException $e) {
                    $this->messageManager->addErrorMessage(
                        __($e->getMessage())
                    );
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. Please try again later.')
                );
                $result->setPath('*/*/form');
                return $result;
            }
        }
        $result->setPath('*/*/index');
        return $result;
    }

    /**
     * @param $post
     * @throws LocalizedException
     */
    private function validatePost($post)
    {
        if (!isset($post['author_name']) || trim($post['author_name']) === '') {
            throw new LocalizedException(__('Name is missing'));
        }
        if (!isset($post['message']) || trim($post['message']) === '') {
            throw new LocalizedException(__('Comment is missing'));
        }
        if (!isset($post['author_email']) || false === strpos($post['author_email'], '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
        if (trim($this->request->getParam('hideit')) !== '') {
            throw new Exception();
        }
    }
}
