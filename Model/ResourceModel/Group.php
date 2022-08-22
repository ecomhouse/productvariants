<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Group extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('product_variant_group', 'group_id');
    }
}

