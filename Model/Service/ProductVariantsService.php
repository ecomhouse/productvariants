<?php

namespace EcomHouse\ProductVariants\Model\Service;

use Magento\Framework\App\ResourceConnection;

class ProductVariantsService
{
    /**
     * @var string
     */
    protected $productRelationTable = 'product_variant_group_relation';

    /**
     * @var string
     */
    protected $optionRelationTable = 'product_variant_options';

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resource = $resourceConnection;
    }

    /**
     * @param $model
     * @param $productsIds
     * @return bool
     */
    public function saveProductsRelation($model, $productsIds): bool
    {
        $id = $model->getId();
        $oldProducts = $this->getProductIds($id);
        $insert = array_diff($productsIds, $oldProducts);
        $delete = array_diff($oldProducts, $productsIds);
        $adapter = $this->resource->getConnection();

        if (!empty($delete)) {
            $condition = ['product_id IN(?)' => $delete, 'group_id=?' => $id];
            $adapter->delete($this->productRelationTable, $condition);
        }

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                if ($tagId) {
                    $data[] = [
                        'group_id' => (int)$id,
                        'product_id' => (int)$tagId,
                    ];
                }
            }
            $adapter->insertMultiple($this->productRelationTable, $data);
        }
        return true;
    }

    /**
     * @param $groupId
     * @return array
     */
    public function getProductIds($groupId): array
    {
        $adapter = $this->resource->getConnection();
        $select = $adapter->select()
            ->from($this->productRelationTable, 'product_id')
            ->where('group_id = ?', $groupId);

        return $adapter->fetchCol($select);
    }

    /**
     * @param $model
     * @param $attributesIds
     * @return bool
     */
    public function saveOptionsRelation($model, $attributesIds)
    {
        $id = $model->getId();
        $oldAttributes = $this->getOptionsIds($id);
        $insert = array_diff($attributesIds, $oldAttributes);
        $delete = array_diff($oldAttributes, $attributesIds);
        $adapter = $this->resource->getConnection();

        if (!empty($delete)) {
            $condition = ['attribute_id IN(?)' => $delete, 'group_id=?' => $id];
            $adapter->delete($this->optionRelationTable, $condition);
        }

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                $data[] = [
                    'group_id' => (int)$id,
                    'attribute_id' => (int)$tagId,
                ];
            }
            $adapter->insertMultiple($this->optionRelationTable, $data);
        }
        return true;
    }

    /**
     * @param $groupId
     * @return array
     */
    public function getOptionsIds($groupId): array
    {
        $adapter = $this->resource->getConnection();
        $select = $adapter->select()
            ->from($this->optionRelationTable, 'attribute_id')
            ->where('group_id = ?', $groupId);

        return $adapter->fetchCol($select);
    }

}
