<?php

namespace Training\FeedbackProduct\Model;

class FeedbackDataLoader
{
    const PRODUCT_ID_FIELD = 'entity_id';
    const PRODUCT_SKU_FIELD = 'sku';
    private $productRepository;
    private $searchCriteriaBuilder;
    private $filterBuilder;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    public function addProductsToFeedbackBySkus($feedback, $skus)
    {
        $feedback->getExtensionAttributes()
            ->setProducts($this->getProductsByField(self::PRODUCT_SKU_FIELD, $skus));
        return $feedback;
    }

    public function addProductsToFeedbackByIds($feedback, $ids)
    {
        $feedback->getExtensionAttributes()
            ->setProducts($this->getProductsByField(self::PRODUCT_ID_FIELD, $ids));
        return $feedback;
    }

    private function getProductsByField($field, $value)
    {
        if (!is_array($value) || !count($value)) {
            return [];
        }
        $skuFilter = $this->filterBuilder
            ->setField($field)
            ->setValue($value)
            ->setConditionType('in')
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$skuFilter])
            ->create();
        return $this->productRepository->getList($searchCriteria)->getItems();
    }
}
