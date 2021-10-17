<?php

namespace Training\Js45\Controller\Product;

use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Qty implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $jsonResultFactory;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var StockItemRepository
     */
    private StockItemRepository $stockItemRepository;

    /**
     * Index constructor.
     * @param JsonFactory $jsonResultFactory
     * @param StockItemRepository $stockItemRepository
     * @param RequestInterface $request
     */
    public function __construct(
        JsonFactory $jsonResultFactory,
        StockItemRepository $stockItemRepository,
        RequestInterface $request
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->request = $request;
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $productId = $this->request->getParam('productId');
        $qty = '';
        if ($productId) {
            try {
                $stock = $this->stockItemRepository->get($productId);
                $qty = $stock->getQty();
            } catch (NoSuchEntityException $e) {
            }
        }
        $result = $this->jsonResultFactory->create();
        $result->setData(["qty" => $qty]);
        return $result;
    }
}
