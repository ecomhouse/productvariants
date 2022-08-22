<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model;

use EcomHouse\ProductVariants\Api\Data\GroupInterface;
use Magento\Framework\Model\AbstractModel;

class Group extends AbstractModel implements GroupInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\EcomHouse\ProductVariants\Model\ResourceModel\Group::class);
    }

    /**
     * @inheritDoc
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * @inheritDoc
     */
    public function getGroupName()
    {
        return $this->getData(self::GROUP_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setGroupName($groupName)
    {
        return $this->setData(self::GROUP_NAME, $groupName);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

