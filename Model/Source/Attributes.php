<?php

namespace EcomHouse\ProductVariants\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as EavCollection;
use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
    /**
     * @var EavCollection
     */
    protected $eavCollection;

    /**
     * @param EavCollection $eavCollection
     */
    public function __construct(EavCollection $eavCollection)
    {
        $this->eavCollection = $eavCollection;
    }

    /**
     * @return array
     */
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

    /**
     * @return EavCollection
     */
    public function getOptionsCollection()
    {
        return $this->eavCollection
            ->addFieldToFilter(Set::KEY_ENTITY_TYPE_ID, 4)
            ->addFieldToFilter('frontend_input', 'select');
    }

}
