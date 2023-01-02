<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\ResourceModel\Group;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const GROUP_PRODUCT_TABLE = 'product_variant_group_relation';
    const GROUP_ATTRIBUTE_TABLE = 'product_variant_options';

    protected $_idFieldName = 'group_id';

    protected function _construct()
    {
        $this->_init(
            \EcomHouse\ProductVariants\Model\Group::class,
            \EcomHouse\ProductVariants\Model\ResourceModel\Group::class
        );
        $this->_map['fields']['product_id'] = 'product_table.product_id';
        $this->_map['fields']['group_id'] = 'main_table.group_id';
    }

    public function filterByProductId(int $productId): Collection
    {
        $this->join(
            ['product_table' => $this->getTable(self::GROUP_PRODUCT_TABLE)],
            'product_table.group_id = main_table.group_id',
            ['product_id']
        );

        return $this->addFieldToFilter('product_id', ['eq' => $productId]);
    }

    public function addIsActiveFilter(): Collection
    {
        return $this->addFieldToFilter('main_table.status', ['eq' => 1]);
    }
}
