<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\ResourceModel\Group\Relation;

use EcomHouse\ProductVariants\Api\Data\GroupInterface;
use EcomHouse\ProductVariants\Model\Group as GroupModel;
use EcomHouse\ProductVariants\Model\ResourceModel\Group;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class SaveHandler implements ExtensionInterface
{
    private MetadataPool $metadataPool;
    private ResourceConnection $resourceConnection;
    private Group $groupResource;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        Group $groupResource
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->groupResource = $groupResource;
    }

    /**
     * @param GroupModel $entity
     */
    public function execute($entity, $arguments = []): GroupModel
    {
        $entityMetadata = $this->metadataPool->getMetadata(GroupInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $connection = $this->resourceConnection->getConnection();

        $this->updateProducts($entity, $linkField, $connection);
        $this->updateAttributes($entity, $linkField, $connection);

        return $entity;
    }

    public function updateProducts(GroupModel $entity, string $linkField, AdapterInterface $connection): void
    {
        $oldProducts = $this->groupResource->lookupProductIds($entity->getGroupId());
        $newProducts = array_filter((array) $entity->getData('product_ids'));

        $table = $this->resourceConnection->getTableName(Group::GROUP_PRODUCT_TABLE);

        $delete = array_diff($oldProducts, $newProducts);
        if ($delete) {
            $where = [
                "$linkField = ?" => $entity->getGroupId(),
                'product_id IN (?)' => $delete
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newProducts, $oldProducts);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $groupId) {
                $data[] = [
                    $linkField => $entity->getGroupId(),
                    'product_id' => (int) $groupId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }

    public function updateAttributes(GroupModel $entity, string $linkField, AdapterInterface $connection): void
    {
        $oldAttributes = $this->groupResource->lookupAttributeIds($entity->getGroupId());
        $newAttributes = array_filter((array) $entity->getData('attribute_ids'));

        $table = $this->resourceConnection->getTableName(Group::GROUP_ATTRIBUTE_TABLE);

        $delete = array_diff($oldAttributes, $newAttributes);
        if ($delete) {
            $where = [
                "$linkField = ?" => $entity->getGroupId(),
                'attribute_id IN (?)' => $delete
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newAttributes, $oldAttributes);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $groupId) {
                $data[] = [
                    $linkField => $entity->getGroupId(),
                    'attribute_id' => (int) $groupId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }
}
