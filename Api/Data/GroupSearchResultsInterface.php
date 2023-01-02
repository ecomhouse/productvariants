<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface GroupSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Group list.
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface[]
     */
    public function getItems(): array;

    /**
     * Set group_name list.
     * @param \EcomHouse\ProductVariants\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items): GroupSearchResultsInterface;
}
