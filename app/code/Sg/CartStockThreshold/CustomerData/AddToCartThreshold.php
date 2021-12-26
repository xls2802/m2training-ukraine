<?php
namespace Sg\CartStockThreshold\CustomerData;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Sg\CartStockThreshold\ViewModel\ViewModelCartStockThreshold;

class AddToCartThreshold implements SectionSourceInterface
{
    /**
     * @var ViewModelCartStockThreshold
     */
    private ViewModelCartStockThreshold $viewModelCartStockThreshold;
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
     * @param ViewModelCartStockThreshold $viewModelCartStockThreshold
     * @param RedirectInterface $redirect
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ViewModelCartStockThreshold $viewModelCartStockThreshold,
        RedirectInterface $redirect,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->viewModelCartStockThreshold = $viewModelCartStockThreshold;
        $this->redirect = $redirect;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->productRepository = $productRepository;
    }

    /**
     * @return string[]
     */
    public function getSectionData()
    {
        $class = '';
        $product = $this->getProductFromRefererUrl();

        if ($product) {
            $isAddToCartBtnEnabled = $this->viewModelCartStockThreshold->isAddToCartBtnEnabled($product);
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
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     * @throws NoSuchEntityException
     */
    public function getProductFromRefererUrl()
    {
        $product = null;
        $storeId = $this->storeManager->getStore()->getId();
        $domain = $this->storeManager->getStore()->getUrl();
        $refererUrl = $this->redirect->getRefererUrl();
        $request_path = explode($domain, $refererUrl)[1];

        $data = [
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => $request_path,
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::STORE_ID => $storeId
        ];

        $urlObj = $this->urlFinder->findOneByData($data);
        if ($urlObj && $urlObj->getEntityType() == 'product') {
            $origUrl = $urlObj->getTargetPath();
            $productId = explode('/', $origUrl)[4];
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }
        }
        return $product;
    }
}
