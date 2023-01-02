<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Data;
use Magento\Swatches\Helper\Data as SwatchHelper;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory;

class GenerateVariants
{
    private ProductRepositoryInterface $productRepository;
    private ProductAttributeRepositoryInterface $productAttributeRepository;
    private Data $catalogHelper;
    private SwatchHelper $swatchHelper;
    private CollectionFactory $swatchCollectionFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        Data $catalogHelper,
        SwatchHelper $swatchHelper,
        CollectionFactory $swatchCollectionFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->catalogHelper = $catalogHelper;
        $this->swatchHelper = $swatchHelper;
        $this->swatchCollectionFactory = $swatchCollectionFactory;
    }

    public function execute(array $productsAttributesData): array
    {
        $attributesData = $this->flattenArray($productsAttributesData);
        $variants = [];
        $currentProduct = $this->catalogHelper->getProduct();

        foreach ($attributesData as $attributeId => $data) {
            $attribute = $this->productAttributeRepository->get($attributeId);
            $attributeCode = $attribute->getAttributeCode();

            foreach ($data as $productId => $attributeValue) {
                $variant = [];
                $product = $this->productRepository->getById((int) $productId);
                $variants[$attributeId]['label'] = $attribute->getDefaultFrontendLabel();
                $variant['path'] = $product->getProductUrl();
                $variant['class'] = $product->isSalable() ? '' : 'disabled';
                $variant['attribute_text'] = $product->getAttributeText($attributeCode);

                if ($productId == $currentProduct->getId() ||
                    $currentProduct->getData($attributeCode) == $attributeValue
                ) {
                    $variant['path'] = '#';
                    $variant['class'] = 'selected';
                }

                $variant['swatch_type'] = 0;
                if ($this->swatchHelper->isSwatchAttribute($attribute)) {
                    $swatchData = $this->getVisualSwatchValue((int) $attributeValue);

                    $variant['swatch_type'] = (int) $swatchData['type'];
                    $variant['swatch_value'] = $swatchData['value'];
                }

                $variants[$attributeId]['values'][] = $variant;
            }
        }

        return $variants;
    }

    private function flattenArray(array $array): array
    {
        $flatData = [];
        foreach ($array as $productId => $attributesData) {
            foreach ($attributesData as $attributeId => $value) {
                if (isset($flatData[$attributeId]) && in_array($value, $flatData[$attributeId])) {
                    continue;
                }
                $flatData[$attributeId][$productId] = $value;
            }
        }

        return $flatData;
    }

    private function getVisualSwatchValue(int $optionId): array
    {
        $swatchCollection = $this->swatchCollectionFactory->create()
            ->addFieldToFilter('option_id', $optionId)
            ->getFirstItem();

        return $swatchCollection->getData();
    }
}
