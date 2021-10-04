<?php

namespace Training\Test\Controller\Product;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class View
 * @package Training\Test\Controller\Product
 */
class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Data
     */
    private $jsonHelper;
    /**
     * @var Session
     */
    private Session $customerSession;
    /**
     * @var RedirectFactory
     */
    private RedirectFactory $redirectFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface|null $logger
     * @param Data|null $jsonHelper
     * @param Session $customerSession
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        LoggerInterface $logger = null,
        Data $jsonHelper = null,
        Session $customerSession,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory, $logger, $jsonHelper);
        $this->customerSession = $customerSession;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
//        if (!$this->customerSession->isLoggedIn()) {
//            $this->messageManager->addNoticeMessage(__('Viewing products is allowed only for logged in customers. Please log in.'));
//            return $this->redirectFactory->create()->setPath('customer/account/login');
//        }
        return parent::execute();
    }
}
