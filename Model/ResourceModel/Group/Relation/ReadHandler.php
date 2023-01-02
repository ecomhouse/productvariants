<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\ResourceModel\Group\Relation;

use EcomHouse\ProductVariants\Model\ResourceModel\Group;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    private MetadataPool $metadataPool;
    private Group $groupResource;

    public function __construct(
        MetadataPool $metadataPool,
        Group $groupResource
    ) {
        $this->metadataPool = $metadataPool;
        $this->groupResource = $groupResource;
    }

    /**
     * @param \EcomHouse\ProductVariants\Model\Group $entity
     */
    public function execute($entity, $arguments = []): \EcomHouse\ProductVariants\Model\Group
    {
        if ($entity->getGroupId()) {
            $productIds = $this->groupResource->lookupProductIds($entity->getGroupId());
            $entity->setData('product_ids', $productIds);

            $attributeIds = $this->groupResource->lookupAttributeIds($entity->getGroupId());
            $entity->setData('attribute_ids', $attributeIds);
        }
        return $entity;
    }
}
