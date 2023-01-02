<?php
declare(strict_types=1);
namespace EcomHouse\ProductVariants\Ui\DataProvider\Group\Form\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Products implements ModifierInterface
{
    const PRODUCTS_SCOPE = 'products';
    const DYNAMIC_ROWS_NAME = 'assigned_products';

    protected ProductRepositoryInterface $productRepository;
    protected ImageHelper $imageHelper;
    protected Status $status;
    protected AttributeSetRepositoryInterface $attributeSetRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository
    ) {
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
    }

    public function modifyData(array $data, int $groupId = null): array
    {
        $productIds = $data[$groupId]['product_ids'];

        $data[$groupId][self::PRODUCTS_SCOPE][self::DYNAMIC_ROWS_NAME] = [];
        foreach ($productIds as $productId) {

            /** @var \Magento\Catalog\Model\Product $linkedProduct */
            $linkedProduct = $this->productRepository->getById($productId);
            $data[$groupId][self::PRODUCTS_SCOPE][self::DYNAMIC_ROWS_NAME][] = $this->fillData($linkedProduct);
        }

        return $data;
    }

    protected function fillData(ProductInterface $linkedProduct): array
    {
        return [
            'entity_id' => $linkedProduct->getId(),
            'name' => $linkedProduct->getName(),
            'status' => $this->status->getOptionText($linkedProduct->getStatus()),
            'attribute_set' => $this->attributeSetRepository
                ->get($linkedProduct->getAttributeSetId())
                ->getAttributeSetName(),
            'sku' => $linkedProduct->getSku(),
            'price' => $linkedProduct->getPrice(),
            'thumbnail' => $this->imageHelper->init($linkedProduct, 'product_listing_thumbnail')->getUrl()
        ];
    }

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
