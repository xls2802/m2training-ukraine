<?php

namespace Training\Test\Controller\Index;

/**
 * Class Index
 * @package Training\Test\Controller\Index
 *
 *
 * could be just implemented Magento\Framework\App\ActionInterface only without extending
 */
class Index extends \Magento\Framework\App\Action\Action
{
    private $resultRawFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents('simple text');
        return $resultRaw;
    }
}
