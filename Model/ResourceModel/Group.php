<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\ResourceModel;

use EcomHouse\ProductVariants\Api\Data\GroupInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Group extends AbstractDb
{
    const GROUP_TABLE_NAME = 'product_variant_group';
    const GROUP_PRODUCT_TABLE = 'product_variant_group_relation';
    const GROUP_ATTRIBUTE_TABLE = 'product_variant_options';

    private EntityManager $entityManager;
    private MetadataPool $metadataPool;
    private ResourceConnection $resourceConnection;

    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    protected function _construct()
    {
        $this->_init(self::GROUP_TABLE_NAME, GroupInterface::GROUP_ID);
    }

    public function lookupProductIds(int $groupId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $entityMetadata = $this->metadataPool->getMetadata(GroupInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['pvgp' => $this->getTable(self::GROUP_PRODUCT_TABLE)], 'product_id')
            ->join(
                ['pvg' => $this->getMainTable()],
                "pvg.$linkField = pvgp.$linkField",
                []
            )
            ->where('pvgp.' . $entityMetadata->getIdentifierField() . ' = :group_id');

        return $connection->fetchCol($select, ['group_id' => $groupId]);
    }

    public function lookupAttributeIds(int $groupId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $entityMetadata = $this->metadataPool->getMetadata(GroupInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['pvga' => $this->getTable(self::GROUP_ATTRIBUTE_TABLE)], 'attribute_id')
            ->join(
                ['pvg' => $this->getMainTable()],
                "pvg.$linkField = pvga.$linkField",
                []
            )
            ->where('pvga.' . $entityMetadata->getIdentifierField() . ' = :group_id');

        return $connection->fetchCol($select, ['group_id' => $groupId]);
    }

    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    public function load(AbstractModel $object, $value, $field = null)
    {
        return $this->entityManager->load($object, $value);
    }

    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
