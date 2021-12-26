<?php
namespace Sg\CartStockThreshold\CustomerData;

use Magento\Catalog\Block\Product\View;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Sg\CartStockThreshold\ViewModel\ViewModelCartStockThreshold;

class AddToCartThreshold implements SectionSourceInterface
{
    /**
     * @var ViewModelCartStockThreshold
     */
    private ViewModelCartStockThreshold $viewModelCartStockThreshold;
    /**
     * @var View
     */
    private View $productViewBlock;

    /**
     * AddToCartThreshold constructor.
     * @param ViewModelCartStockThreshold $viewModelCartStockThreshold
     * @param View $productViewBlock
     */
    public function __construct(
        ViewModelCartStockThreshold $viewModelCartStockThreshold,
        View $productViewBlock
    ) {
        $this->viewModelCartStockThreshold = $viewModelCartStockThreshold;
        $this->productViewBlock = $productViewBlock;
    }

    /**
     * @return string[]
     */
    public function getSectionData()
    {
//        $product = $this->productViewBlock->getProduct();
//        $isAddToCartBtnEnabled = $this->viewModelCartStockThreshold->isAddToCartBtnEnabled($product);
//        $class = '';
//        if (!$isAddToCartBtnEnabled) {
//            $class = 'disabled';
//        }
        return [
            'class' => 'ololo'//$class
        ];
    }
}
