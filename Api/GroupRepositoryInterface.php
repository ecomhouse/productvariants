<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Api;

interface GroupRepositoryInterface
{
    /**
     * Save Group
     * @param \EcomHouse\ProductVariants\Api\Data\GroupInterface $group
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \EcomHouse\ProductVariants\Api\Data\GroupInterface $group
    );

    /**
     * Retrieve Group
     * @param string $groupId
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groupId);

    /**
     * Retrieve Group matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \EcomHouse\ProductVariants\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Group
     * @param \EcomHouse\ProductVariants\Api\Data\GroupInterface $group
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \EcomHouse\ProductVariants\Api\Data\GroupInterface $group
    );

    /**
     * Delete Group by ID
     * @param string $groupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groupId);
}

