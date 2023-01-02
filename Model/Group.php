<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model;

use EcomHouse\ProductVariants\Api\Data\GroupInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @method array getAttributeIds()
 * @method Group setAttributeIds(array $attributeIds)
 * @method array getProductIds()
 * @method Group setProductIds(array $productIds)
 */
class Group extends AbstractModel implements GroupInterface, IdentityInterface
{
    const CACHE_TAG = 'ecomhouse_productvariants_group';

    protected $_cacheTag = 'ecomhouse_productvariants_group';

    protected $_eventPrefix = 'ecomhouse_productvariants_group';

    public function _construct()
    {
        $this->_init(ResourceModel\Group::class);
    }

    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getGroupId(): ?int
    {
        $id = $this->getData(self::GROUP_ID);

        return $id ? (int) $id : null;
    }

    public function setGroupId(int $groupId): GroupInterface
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    public function getGroupName(): string
    {
        return $this->getData(self::GROUP_NAME);
    }

    public function setGroupName(string $groupName): GroupInterface
    {
        return $this->setData(self::GROUP_NAME, $groupName);
    }

    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus(string $status): GroupInterface
    {
        return $this->setData(self::STATUS, $status);
    }
}
