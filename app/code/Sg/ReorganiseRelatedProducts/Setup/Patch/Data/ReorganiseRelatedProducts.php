<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sg\ReorganiseRelatedProducts\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Psr\Log\LoggerInterface;

class ReorganiseRelatedProducts implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * ReorganiseRelatedProducts constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->resourceConnection->getConnection();
        $tableLinkType = $connection->getTableName('catalog_product_link_type');
        $tableLink = $connection->getTableName('catalog_product_link');

        $queryUpSell = "Select `link_type_id` FROM " . $tableLinkType . " WHERE code = 'up_sell'";
        $queryRelation = "Select link_type_id FROM " . $tableLinkType . " WHERE code = 'relation'";

        $linkedIdUpSell = $connection->fetchOne($queryUpSell);
        $linkedIdRelation = $connection->fetchOne($queryRelation);

        $query = "UPDATE `" . $tableLink . "` SET `link_type_id`= $linkedIdRelation WHERE `link_type_id` = $linkedIdUpSell ";
        $connection->query($query);
        $this->moduleDataSetup->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
