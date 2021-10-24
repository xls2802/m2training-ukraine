<?php

namespace Training\CustomerPersonalSite\ViewModel;

use Magento\Framework\UrlInterface;

class CustomerAttribute implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public function getPersonalSite($customerData)
    {
        $attribute = $customerData->getCustomAttribute('personal_site');
        if ($attribute) {
            return $attribute->getValue();
        }
        return '';

    }
}
