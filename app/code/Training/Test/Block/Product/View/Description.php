<?php
namespace Training\Test\Block\Product\View;

class Description
{
    public function beforeToHtml(
        \Magento\Catalog\Block\Product\View\Description $subject
    ) {
        if ($subject->getNameInLayout() == 'product.info.sku') {
            $subject->setTemplate('Training_Test::description.phtml');
        }
    }
}
