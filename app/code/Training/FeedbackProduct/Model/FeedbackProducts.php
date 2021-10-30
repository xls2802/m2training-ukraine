<?php

namespace Training\FeedbackProduct\Model;

class FeedbackProducts
{
    private $feedbackDataLoader;
    private $feedbackProductsResource;

    /**
     * FeedbackProducts constructor.
     * @param FeedbackDataLoader $feedbackDataLoader
     * @param ResourceModel\FeedbackProducts $feedbackProductsResource
     */
    public function __construct(
        \Training\FeedbackProduct\Model\FeedbackDataLoader $feedbackDataLoader,
        \Training\FeedbackProduct\Model\ResourceModel\FeedbackProducts $feedbackProductsResource
    ) {
        $this->feedbackDataLoader = $feedbackDataLoader;
        $this->feedbackProductsResource = $feedbackProductsResource;
    }

    /**
     * @param $feedback
     * @return mixed
     */
    public function loadProductRelations($feedback)
    {
        $productIds = $this->feedbackProductsResource->loadProductRelations($feedback->getId());
        return $this->feedbackDataLoader->addProductsToFeedbackByIds($feedback, $productIds);
    }

    /**
     * @param $feedback
     * @return $this
     */
    public function saveProductRelations($feedback)
    {
        $productIds = [];
        $products = $feedback->getExtensionAttributes()->getProducts();
        if (is_array($products)) {
            foreach ($products as $product) {
                $productIds[] = $product->getId();
            }
        }
        $this->feedbackProductsResource->saveProductRelations($feedback->getId(), $productIds);
        return $this;
    }
}
