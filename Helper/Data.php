<?php

namespace EcomHouse\ProductVariants\Helper;

use EcomHouse\ProductVariants\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Data as SwatchesHelper;

class Data extends AbstractHelper
{
    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var SwatchesHelper
     */
    protected $swatchHelper;

    /**
     * @param Context $context
     * @param UrlInterface $backendUrl
     * @param ObjectManagerInterface $objectManager
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Config $eavConfig
     * @param SwatchesHelper $swatchHelper
     */
    public function __construct(
        Context                $context,
        UrlInterface           $backendUrl,
        ObjectManagerInterface $objectManager,
        GroupCollectionFactory $groupCollectionFactory,
        StoreManagerInterface  $storeManager,
        Config                 $eavConfig,
        SwatchesHelper         $swatchHelper
    )
    {
        $this->backendUrl = $backendUrl;
        $this->objectManager = $objectManager;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->storeManager = $storeManager;
        $this->eavConfig = $eavConfig;
        $this->swatchHelper = $swatchHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isOnlySimpleProducts(): bool
    {
        return (bool)$this->scopeConfig->getValue('ecomhouse_productvariants/general/only_simple');
    }

    /**
     * @param $product
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getOptionsVariantsCollection($product): array
    {
        if ($this->isOnlySimpleProducts() && $product->getTypeId() !== Type::TYPE_SIMPLE) {
            return [];
        }

        $productId = $product->getId();
        $groupCollection = $this->getGroupsByProductId($productId);
        $groupId = null;
        $optionsIds = [];
        foreach ($groupCollection as $element) {
            $groupId = (int)$element['group_id'];
            $optionsIds[] = $element['attribute_id'];
        }

        $countOptionsIds = count($optionsIds);
        if ($countOptionsIds === 0) {
            return [];
        }

        $collectionArray = $this->getProductsAndOptionsArray($groupId, $optionsIds);
        $result = [];
        $data = [];
        foreach ($collectionArray as $key => $element) {
            $product = $this->objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface')->getById($element['product']);
            if ($this->isEnabled($product)) {
                $data[$element['product']][] = $element['value_id'];
            } else {
                unset($collectionArray[$key]);
            }
        }

        if (!isset($data[$productId])) {
            return [];
        }

        $currentProduct = $data[$productId];

        if ($countOptionsIds === 1) {
            $prevElement = null;
            foreach ($collectionArray as $key => $element) {
                if ($prevElement && $element['value_id'] === $prevElement['value_id']) {
                    unset($collectionArray[$key]);
                }
                $prevElement = $element;
            }

        } else {
            $common = $this->getCommon($data, $currentProduct);
            $prevElement = null;
            foreach ($collectionArray as $key => $element) {
                if (($element['product'] !== $productId && !in_array($element['value_id'], $currentProduct)
                        && in_array($element['product'], $common)) || $element['product'] === $productId) {
                    if ($prevElement && $element['value_id'] === $prevElement['value_id']) {
                        $countPrevElement = count(array_intersect($data[$prevElement['product']], $currentProduct));
                        $countElement = count(array_intersect($data[$element['product']], $currentProduct));

                        if ($countPrevElement > $countElement) {
                            unset($collectionArray[$key]);
                        } elseif ($countPrevElement < $countElement) {
                            unset($collectionArray[$key - 1]);
                        } else {
                            unset($collectionArray[$key - 1]);
                            unset($collectionArray[$key]);
                        }
                    }
                    $prevElement = $element;
                } else {
                    unset($collectionArray[$key]);
                }
            }
        }

        $lastAttribute = null;
        $isSwatchAttribute = true;
        foreach ($collectionArray as $element) {
            $product = $this->objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface')->getById($element['product']);
            $class = 'selected';
            if ($element['product'] !== $productId) {
                $class = '';
                $countElement = count(array_intersect($data[$element['product']], $currentProduct));
                if ($countElement < ($countOptionsIds - 1)) {
                    $class = 'disabled';
                }
            }

            $element['swatch_type'] = 0;
            if ($element['attribute_id'] !== $lastAttribute) {
                $isSwatchAttribute = $this->isSwatchAttribute($element['attribute_code']);
            }

            if ($isSwatchAttribute) {
                $swatchData = $this->getVisualSwatchValue($element['value_id']);
                if ($swatchData) {
                    $element['swatch_type'] = (int)$swatchData['type'];
                    $element['swatch_value'] = $swatchData['value'];
                }
            }

            $element['class'] = $this->isAvailable($product) ? $class : 'disabled';
            $element['attribute_text'] = $product->getAttributeText($element['attribute_code']);
            $element['path'] = ($element['product'] === $productId) ? '#' : $product->getProductUrl();
            $result[$element['attribute_id']]['value'][] = $element;
            $result[$element['attribute_id']]['label'] = $element['label'];
            $lastAttribute = $element['attribute_id'];
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getAttributeSetCollection()
    {
        $collection = $this->objectManager->get('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory');
        $attributeSetCollection = $collection->create();
        $attributeSetCollection->addFieldToFilter('entity_type_id', 4);
        $attributeSetCollection->setOrder('attribute_set_name', 'ASC');

        return $attributeSetCollection;
    }

    /**
     * @param $productId
     * @return mixed
     */
    protected function getGroupsByProductId($productId)
    {
        $collection = $this->groupCollectionFactory->create();

        $collection->join(
            ['pvgr' => $collection->getTable('product_variant_group_relation')],
            'main_table.group_id = pvgr.group_id AND pvgr.product_id = ' . $productId
        );

        $collection->join(
            ['pvo' => $collection->getTable('product_variant_options')],
            'pvo.group_id = main_table.group_id'
        );

        $collection->addFieldToFilter('main_table.status', true);

        return $collection->getData();
    }

    /**
     * @param $groupId
     * @param $optionsIds
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductsAndOptionsArray($groupId, $optionsIds): array
    {
        $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $storeId = $this->storeManager->getStore()->getStoreId();
        $storeId = $storeId ?? 1;

        //Select Data from table
        $sql = "SELECT ce.entity_id as product, ce.sku, ea.attribute_id as attribute_id,
         ea.attribute_code as attribute_code,
            IF (eal.value IS NOT NULL, eal.value, ea.frontend_label) as label,
            CASE ea.backend_type
               WHEN 'int' THEN ce_int.value
               ELSE ea.backend_type
            END AS value_id
        FROM (SELECT cpe.sku, eas.entity_type_id, cpe.entity_id
                    FROM catalog_product_entity AS cpe, eav_attribute_set AS eas
                    WHERE cpe.attribute_set_id = eas.attribute_set_id
             AND cpe.entity_id IN (SELECT pvgr.product_id FROM product_variant_group pvg
                                    JOIN product_variant_group_relation pvgr ON pvgr.group_id = pvg.group_id
                                    WHERE pvg.group_id = " . $groupId . "
                                    AND pvg.status = 1)
             ) AS ce
        LEFT JOIN eav_attribute AS ea
            ON ce.entity_type_id = ea.entity_type_id
        LEFT JOIN eav_attribute_label AS eal
            ON ea.attribute_id = eal.attribute_id AND eal.store_id = " . $storeId . "
        LEFT JOIN catalog_product_entity_int AS ce_int
            ON ce.entity_id = ce_int.entity_id
            AND ea.attribute_id = ce_int.attribute_id
            AND ea.backend_type = 'int'
        WHERE ea.attribute_id IN (" . implode(",", $optionsIds) . ")
        AND ce_int.value IS NOT NULL
        ORDER BY attribute_id, ce_int.value";

        return $connection->fetchAll($sql);
    }

    /**
     * @param $data
     * @param $currentProduct
     * @return array
     */
    protected function getCommon($data, $currentProduct): array
    {
        $common = [];
        if (isset($currentProduct)) {
            foreach ($data as $key => $value) {
                foreach ($currentProduct as $search) {
                    if (in_array($search, $value)) {
                        $common[] = $key;
                    }
                }
            }
        }

        return $common;
    }

    protected function isEnabled($product): bool
    {
        if ((int)$product->getStatus() === 1 && (int)$product->getVisibility() !== Visibility::VISIBILITY_NOT_VISIBLE) {
            return true;
        }

        return false;
    }

    /**
     * @param $product
     * @return bool
     */
    protected function isAvailable($product): bool
    {
        $stockItem = $product->getExtensionAttributes()->getStockItem();
        if ($stockItem->getQty() > 0 && $stockItem->getIsInStock()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $code
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isSwatchAttribute(string $code): bool
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $code);
        return $this->swatchHelper->isSwatchAttribute($attribute);
    }

    /**
     * @param $optionId
     * @return false|mixed
     */
    private function getVisualSwatchValue($optionId)
    {
        $swatchCollection = $this->objectManager->create('Magento\Swatches\Model\ResourceModel\Swatch\Collection');
        $swatchCollection->join(
            ['eaos' => $swatchCollection->getTable('eav_attribute_option_swatch')],
            'main_table.option_id = eaos.option_id'
        );

        $swatchCollection->addFieldToFilter('main_table.option_id', $optionId);
        if ($swatchCollection->getSize() === 1) {
            return $swatchCollection->getData()[0];
        }
        return false;
    }

}
