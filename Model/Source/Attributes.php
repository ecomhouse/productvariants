<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\Source;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as EavCollection;
use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
    protected EavCollection $eavCollection;
    private Config $config;

    public function __construct(
        EavCollection $eavCollection,
        Config $config
    ) {
        $this->eavCollection = $eavCollection;
        $this->config = $config;
    }

    public function toOptionArray(): array
    {
        $optionById = [];
        $collection = $this->getOptionsCollection();
        foreach ($collection as $option) {
            $optionById[] = [
                'value' => $option->getId(),
                'label' => $option->getFrontendLabel()
            ];
        }

        return $optionById;
    }

    public function getOptionsCollection(): EavCollection
    {
        $productEntityTypeId = $this->config->getEntityType(Product::ENTITY)->getEntityTypeId();
        return $this->eavCollection
            ->addFieldToFilter(Set::KEY_ENTITY_TYPE_ID, $productEntityTypeId)
            ->addFieldToFilter('frontend_input', 'select');
    }
}
