<?php

namespace Training\FeedbackProduct\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadFeedbackProducts implements ObserverInterface
{
    /**
     * @var \Training\FeedbackProduct\Model\FeedbackProducts
     */
    private $feedbackProducts;

    /**
     * LoadFeedbackProducts constructor.
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
        $this->feedbackProducts->loadProductRelations($feedback);
    }
}
