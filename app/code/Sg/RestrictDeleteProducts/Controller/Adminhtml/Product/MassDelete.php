<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sg\RestrictDeleteProducts\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Controller\Adminhtml\Product\MassDelete as MassDeleteCore;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class \Magento\Catalog\Controller\Adminhtml\Product\MassDelete
 */
class MassDelete extends MassDeleteCore implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Sg_RestrictDeleteProducts::delete_products';
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface|null $productRepository
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct(
            $context,
            $productBuilder,
            $filter,
            $collectionFactory,
            $productRepository,
            $logger
        );

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository ?:
            ObjectManager::getInstance()->create(ProductRepositoryInterface::class);
        $this->logger = $logger ?:
            ObjectManager::getInstance()->create(LoggerInterface::class);
    }

    /**
     * Mass Delete Action
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->_authorization->isAllowed('Sg_RestrictDeleteProducts::delete_products')) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collection->addMediaGalleryData();
            $productDeleted = 0;
            $productDeletedError = 0;
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($collection->getItems() as $product) {
                try {
                    $this->productRepository->delete($product);
                    $productDeleted++;
                } catch (LocalizedException $exception) {
                    $this->logger->error($exception->getLogMessage());
                    $productDeletedError++;
                }
            }
            if ($productDeleted) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', $productDeleted)
                );
            }
            if ($productDeletedError) {
                $this->messageManager->addErrorMessage(
                    __(
                        'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                        $productDeletedError
                    )
                );
            }
        } else {
            $this->messageManager->addNoticeMessage(
                __('You have no permission for this action')
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sg_RestrictDeleteProducts::delete_products');
    }
}
