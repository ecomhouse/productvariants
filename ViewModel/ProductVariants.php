<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\ViewModel;

use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use EcomHouse\ProductVariants\Model\GenerateVariants;
use EcomHouse\ProductVariants\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Catalog\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class ProductVariants implements ArgumentInterface
{
    private Data $catalogHelper;
    private CollectionFactory $productCollectionFactory;
    private GroupCollectionFactory $groupCollectionFactory;
    private GroupRepositoryInterface $groupRepository;
    private ProductResource $productResource;
    private StoreManagerInterface $storeManager;
    private GenerateVariants $generateVariants;

    public function __construct(
        Data $catalogHelper,
        CollectionFactory $productCollectionFactory,
        GroupCollectionFactory $groupCollectionFactory,
        GroupRepositoryInterface $groupRepository,
        ProductResource $productResource,
        StoreManagerInterface $storeManager,
        GenerateVariants $generateVariants
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->groupRepository = $groupRepository;
        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
        $this->generateVariants = $generateVariants;
    }

    public function getOptionsVariantsArray(): array
    {
        $currentProductId = (int) $this->catalogHelper->getProduct()->getId();
        $groupIds = $this->groupCollectionFactory->create()
            ->filterByProductId($currentProductId)
            ->addIsActiveFilter()
            ->getAllIds();

        $allProductsData = [];

        foreach ($groupIds as $id) {
            try {
                $group = $this->groupRepository->get((int)$id);

                foreach ($group->getProductIds() as $productId) {
                    $productAttributes = $this->getProductAttributesData(
                        (int) $productId,
                        $group->getAttributeIds()
                    );

                    if (count($productAttributes) === count($group->getAttributeIds())) {
                        $allProductsData[$productId] = $productAttributes;
                    }
                }

                if (count($group->getAttributeIds()) === 1) {
                    return $this->generateVariants->execute($allProductsData);
                }

            } catch (LocalizedException $e) {
                return [];
            }

        }

        $commonAttributesData = [];

        foreach ($allProductsData as $productId => $attributesData) {
            if ($productId == $currentProductId) {
                $commonAttributesData[$currentProductId] = $attributesData;
                continue;
            }
            foreach ($attributesData as $attributeId => $attributeValue) {
                if ($allProductsData[$currentProductId][$attributeId] == $attributeValue) {
                    if (!isset($commonAttributesData[$productId])) {
                        $commonAttributesData[$productId] = $attributesData;
                    }
                }
            }
        }

        return $this->generateVariants->execute($commonAttributesData);
    }

    private function getProductAttributesData(int $productId, array $attributeIds): array
    {
        $storeId = $this->storeManager->getStore()->getId();
        $data = [];

        foreach ($attributeIds as $attributeId) {
            $attributeValue = $this->productResource->getAttributeRawValue(
                $productId,
                $attributeId,
                $storeId
            );
            if ($attributeValue) {
                $data[$attributeId] = $attributeValue;
            }
        }

        return $data;
    }
}
