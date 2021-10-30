<?php

namespace Training\FeedbackProduct\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveFeedbackProducts implements ObserverInterface
{
    private $feedbackProducts;

    /**
     * SaveFeedbackProducts constructor.
     * @param \Training\FeedbackProduct\Model\FeedbackProducts $feedbackProducts
     */
    public function __construct(
        \Training\FeedbackProduct\Model\FeedbackProducts $feedbackProducts
    ) {
        $this->feedbackProducts = $feedbackProducts;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $feedback = $observer->getFeedback();
        $this->feedbackProducts->saveProductRelations($feedback);
    }
}
