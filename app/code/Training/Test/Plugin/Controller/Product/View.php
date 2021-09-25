<?php

namespace Training\Test\Plugin\Controller\Product;

/**
 * Class View
 * @package Training\Test\Plugin\Controller\Product
 */
class View
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * View constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->customerSession = $customerSession;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param \Magento\Catalog\Controller\Product\View $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     */
    public function aroundExecute(
        \Magento\Catalog\Controller\Product\View $subject,
        \Closure $proceed
    ) {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->redirectFactory->create()->setPath('customer/account/login');
        }
        return $proceed();
    }
}
