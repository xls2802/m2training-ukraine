<?php

namespace Training\TestOM\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Training\TestOM\Model\TestFactory;

class Index implements ActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var TestFactory
     */
    protected TestFactory $testFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param TestFactory $testFactory
     */
    public function __construct(
        PageFactory $resultPageFactory,
        TestFactory $testFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->testFactory = $testFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $testModelObj = $this->testFactory->create();

        $testModelObj->log();
        die();

        //return $this->resultPageFactory->create();
    }
}
