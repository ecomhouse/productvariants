<?php

namespace EcomHouse\ProductVariants\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $installer = $setup;
        $installer->startSetup();

        $productVariantGroupTable = 'product_variant_group';
        $productVariantGroupRelationTable = 'product_variant_group_relation';
        $productVariantOptionsTable = 'product_variant_options';
        $productTable = 'catalog_product_entity';
        $attributeTable = 'eav_attribute';

        if (version_compare($context->getVersion(), '1.0.0') < 0) {

            if (!$installer->tableExists($productVariantGroupTable)) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable($productVariantGroupTable)
                )->addColumn(
                    'group_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Product variant group ID'
                )->addColumn(
                    'group_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Product variant group name'
                )->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false, 'default' => '0'],
                    'Product variant group status'
//                )->addColumn(
//                    'attribute_set_id',
//                    Table::TYPE_INTEGER,
//                    10,
//                    ['nullable' => false, 'default' => '0'],
//                    'Attribute set id'
                )
                ->setComment('Product variant group table');

                $installer->getConnection()->createTable($table);
            }

            if (!$installer->tableExists($productVariantGroupRelationTable)) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable($productVariantGroupRelationTable))
                    ->addColumn('group_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true])
                    ->addColumn('product_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Product Id')
                    ->addForeignKey(
                        $installer->getFkName(
                            $productVariantGroupTable,
                            'group_id',
                            $productVariantGroupRelationTable,
                            'group_id'
                        ),
                        'group_id',
                        $installer->getTable($productVariantGroupTable),
                        'group_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            $productVariantGroupRelationTable,
                            'product_id',
                            $productTable,
                            'entity_id'
                        ),
                        'product_id',
                        $installer->getTable($productTable),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Product variant group relation table');

                $installer->getConnection()->createTable($table);
            }

            if (!$installer->tableExists($productVariantOptionsTable)) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable($productVariantOptionsTable)
                )->addColumn(
                    'option_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Product variant option ID'
                )->addColumn(
                    'group_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable' => false],
                    'Product variant group id'
                )->addColumn(
                    'attribute_id',
                    Table::TYPE_SMALLINT,
                    5,
                    ['unsigned' => true, 'nullable' => false],
                    'Eav Attribute id'
                )->addForeignKey(
                    $installer->getFkName(
                        $productVariantOptionsTable,
                        'group_id',
                        $productVariantGroupTable,
                        'group_id'
                    ),
                    'group_id',
                    $installer->getTable($productVariantGroupTable),
                    'group_id',
                    Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName(
                        $productVariantOptionsTable,
                        'attribute_id',
                        $attributeTable,
                        'attribute_id'
                    ),
                    'attribute_id',
                    $installer->getTable($attributeTable),
                    'attribute_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Product variant options table');

                $installer->getConnection()->createTable($table);
            }
        }

        $installer->endSetup();
    }
}
