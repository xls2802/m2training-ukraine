<?php

namespace Training\Test\Controller\Block;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\LayoutFactory;

/**
 * Class Index
 * @package Training\Test\Controller\Block
 */
class Index implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Index constructor.
     * @param LayoutFactory $layoutFactory
     * @param ResponseInterface $response
     */
    public function __construct(
        LayoutFactory $layoutFactory,
        ResponseInterface $response
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->response = $response;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $layout = $this->layoutFactory->create();
        $block = $layout->createBlock('Training\Test\Block\Test');
        $this->response->appendBody($block->toHtml());
        return $this->response;
    }
}
