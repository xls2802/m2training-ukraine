<?php

namespace Sg\CartStockThreshold\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Used for receiving customer data info and also could be used in a template if it is needed for public content
 * see \Sg\CartStockThreshold\CustomerData\AddToCartThreshold
 * Class ViewModelCartStockThreshold
 * @package Sg\CartStockThreshold\ViewModel
 */
class ViewModelCartStockThreshold implements ArgumentInterface
{
    const CONFIG_PATH_CART_STOCK_THRESHOLD_ENABLED = 'sales/cart_stock_threshold/enable';
    const CONFIG_PATH_CART_STOCK_THRESHOLD_QTY = 'sales/cart_stock_threshold/qty_threshold';
    const CONFIG_PATH_CART_STOCK_THRESHOLD_CUSTOMER_GROUPS = 'sales/cart_stock_threshold/customer_groups';
    /**
     * @var GetSalableQuantityDataBySku
     */
    private GetSalableQuantityDataBySku $getSalableQuantityDataBySku;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * ViewModelCartStockThreshold constructor.
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     */
    public function __construct(
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession
    ) {
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isAddToCartBtnEnabled($product)
    {
        $result = true;
        $isEnabled = $this->getConfig(self::CONFIG_PATH_CART_STOCK_THRESHOLD_ENABLED);
        $currentCustomerGroupId = $this->getCurrentCustomerGroupId();
        $customerGroupsForValidation = $this->getCustomerGroupsForValidation();

        if ($isEnabled && (in_array($currentCustomerGroupId, $customerGroupsForValidation) || in_array(GroupInterface::CUST_GROUP_ALL, $customerGroupsForValidation))) {
            $salableQty = $this->getProductSalableQty($product);
            $threshold = $this->getConfig(self::CONFIG_PATH_CART_STOCK_THRESHOLD_QTY);

            $result = $salableQty > (int) $threshold;
        }

        return $result;
    }

    /**
     * Return qty of product from all available stocks (working with multistock)
     * @param $product
     * @return int|mixed
     */
    public function getProductSalableQty($product)
    {
        $stocks = $this->getSalableQuantityDataBySku->execute($product->getSku());
        $qty = 0;
        foreach ($stocks as $stock) {
            if ($stock['qty'] && $stock['manage_stock']) {
                $qty+=$stock['qty'];
            }
        }
        return $qty;
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getConfig(string $path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * @return int
     */
    public function getCurrentCustomerGroupId()
    {
        return $this->customerSession->getCustomer()->getGroupId();
    }

    /**
     * @return false|string[]
     */
    public function getCustomerGroupsForValidation()
    {
        return explode(',', $this->getConfig(self::CONFIG_PATH_CART_STOCK_THRESHOLD_CUSTOMER_GROUPS));
    }
}
