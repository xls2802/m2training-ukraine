<?php

namespace Training\Test\Plugin\Model;

/**
 * Class Url
 * @package Training\Test\Plugin\Model
 */
class Url
{
    /**
     * @param \Magento\Framework\UrlInterface $subject
     * @param null $routePath
     * @param null $routeParams
     * @return array
     */
    public function beforeGetUrl(
        \Magento\Framework\UrlInterface $subject,
        $routePath = null,
        $routeParams = null
    ) {
        if ($routePath == 'customer/account/create') {
            return ['customer/account/login', null];
        }
    }
}
