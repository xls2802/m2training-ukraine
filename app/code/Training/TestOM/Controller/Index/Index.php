<?php

namespace Training\TestOM\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Training\TestOM\Model\PlayWithTest;

class Index implements ActionInterface
{
    /**
     * @var PlayWithTest
     */
    private PlayWithTest $playWithTest;
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * Index constructor.
     * @param PageFactory $resultPageFactory
     * @param PlayWithTest $playWithTest
     */
    public function __construct(
        PageFactory $resultPageFactory,
        PlayWithTest $playWithTest
    ) {
        $this->playWithTest = $playWithTest;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->playWithTest->run();
        die();

        //return $this->resultPageFactory->create();
    }
}
