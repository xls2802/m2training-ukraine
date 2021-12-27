<?php
declare(strict_types=1);

namespace Sg\CartStockThreshold\CustomerData;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Sg\CartStockThreshold\Model\CartStockThreshold;

class AddToCartThreshold implements SectionSourceInterface
{
    /**
     * @var CartStockThreshold
     */
    private CartStockThreshold $modelCartStockThreshold;
    /**
     * @var RedirectInterface
     */
    private RedirectInterface $redirect;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var UrlFinderInterface
     */
    private UrlFinderInterface $urlFinder;
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * AddToCartThreshold constructor.
     * @param CartStockThreshold $modelCartStockThreshold
     * @param RedirectInterface $redirect
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CartStockThreshold $modelCartStockThreshold,
        RedirectInterface $redirect,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->modelCartStockThreshold = $modelCartStockThreshold;
        $this->redirect = $redirect;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getSectionData(): array
    {
        $class = '';
        $product = $this->getProductFromRefererUrl();

        if ($product) {
            $isAddToCartBtnEnabled = $this->modelCartStockThreshold->isAddToCartBtnEnabled($product);
            if (!$isAddToCartBtnEnabled) {
                $class = 'disabled';
            }
        }
        return [
            'class' => $class
        ];
    }

    /**
     * Used this approach because Magento\Catalog\Block\Product\View or registry or request
     * has no info about current product when Customer Data sections loaded
     *
     * @return ProductInterface|null
     * @throws NoSuchEntityException
     */
    public function getProductFromRefererUrl(): ?ProductInterface
    {
        $product = null;
        $storeId = $this->storeManager->getStore()->getId();
        $domain = $this->storeManager->getStore()->getUrl();
        $refererUrl = $this->redirect->getRefererUrl();
        $explodedPath = explode($domain, $refererUrl);
        $requestPath = isset($explodedPath[1]) ? $explodedPath[1] : null;
        $urlObj = null;

        if ($requestPath) {
            $data = [
                UrlRewrite::REQUEST_PATH => $requestPath,
                UrlRewrite::STORE_ID => $storeId
            ];
            $urlObj = $this->urlFinder->findOneByData($data);
        }

        if ($urlObj && $urlObj->getEntityType() === 'product') {
            $productId = $urlObj->getEntityId();
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }
        }

        return $product;
    }
}
