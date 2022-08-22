<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\ResourceModel\Group;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'group_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \EcomHouse\ProductVariants\Model\Group::class,
            \EcomHouse\ProductVariants\Model\ResourceModel\Group::class
        );
    }
}

