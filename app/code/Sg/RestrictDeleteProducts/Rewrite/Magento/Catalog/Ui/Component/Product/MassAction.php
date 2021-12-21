<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sg\RestrictDeleteProducts\Rewrite\Magento\Catalog\Ui\Component\Product;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;

class MassAction extends \Magento\Catalog\Ui\Component\Product\MassAction
{
    const NAME = 'massaction';

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * Constructor
     *
     * @param AuthorizationInterface $authorization
     * @param ContextInterface $context
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        AuthorizationInterface $authorization,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->authorization = $authorization;
        parent::__construct($authorization, $context, $components, $data);
    }

//    /**
//     * @inheritdoc
//     */
//    public function prepare() : void
//    {
//        $config = $this->getConfiguration();
//
//        foreach ($this->getChildComponents() as $actionComponent) {
//            $actionType = $actionComponent->getConfiguration()['type'];
//            if ($this->isActionAllowed($actionType)) {
//                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
//                $config['actions'][] = array_merge($actionComponent->getConfiguration());
//            }
//        }
//        $origConfig = $this->getConfiguration();
//        if ($origConfig !== $config) {
//            $config = array_replace_recursive($config, $origConfig);
//        }
//
//        $this->setData('config', $config);
//        $this->components = [];
//
//        parent::prepare();
//    }

//    /**
//     * @inheritdoc
//     */
//    public function getComponentName() : string
//    {
//        return static::NAME;
//    }

    /**
     * Check if the given type of action is allowed
     *
     * @param string $actionType
     * @return bool
     */
    public function isActionAllowed($actionType) : bool
    {
        $isAllowed = true;
        switch ($actionType) {
            case 'delete':
                $isAllowed = $this->authorization->isAllowed('Sg_RestrictDeleteProducts::delete_products');
                break;
            case 'status':
                $isAllowed = $this->authorization->isAllowed('Magento_Catalog::products');
                break;
            case 'attributes':
                $isAllowed = $this->authorization->isAllowed('Magento_Catalog::update_attributes');
                break;
            default:
                break;
        }
        return $isAllowed;
    }

}
