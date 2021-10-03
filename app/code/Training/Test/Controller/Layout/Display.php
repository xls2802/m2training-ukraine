<?php

namespace Training\Test\Controller\Layout;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory as PageResultFactory;

/**
 * View a product on storefront. Needs to be accessible by POST because of the store switching
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Display implements HttpGetActionInterface, HttpPostActionInterface
{

    /**
     * @var PageResultFactory
     */
    private PageResultFactory $resultPageFactory;
    /**
     * @var RawFactory
     */
    private RawFactory $resultRawFactory;

    public function __construct(
        PageResultFactory $resultPageFactory,
        RawFactory $resultRawFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        //$page->addHandle(['customer_account_login']);
        $page->getLayout()->generateXml();
        $stringLayout = $page->getLayout()->getUpdate()->asString();

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents('<textarea>' . $stringLayout . '</textarea>');
        return $resultRaw;
    }
}
