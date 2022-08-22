<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Api\Data;

interface GroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Group list.
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Set group_name list.
     * @param \EcomHouse\ProductVariants\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

