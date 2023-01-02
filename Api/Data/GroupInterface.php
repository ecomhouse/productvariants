<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Api\Data;

interface GroupInterface
{
    const GROUP_ID = 'group_id';
    const GROUP_NAME = 'group_name';
    const STATUS = 'status';

    /**
     * Get group_id
     * @return int|null
     */
    public function getGroupId(): ?int;

    /**
     * Set group_id
     * @param int $groupId
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface
     */
    public function setGroupId(int $groupId): \EcomHouse\ProductVariants\Api\Data\GroupInterface;

    /**
     * Get group_name
     * @return string|null
     */
    public function getGroupName(): string;

    /**
     * Set group_name
     * @param string $groupName
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface
     */
    public function setGroupName(string $groupName): \EcomHouse\ProductVariants\Api\Data\GroupInterface;

    /**
     * Get status
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Set status
     * @param string $status
     * @return \EcomHouse\ProductVariants\Api\Data\GroupInterface
     */
    public function setStatus(string $status): \EcomHouse\ProductVariants\Api\Data\GroupInterface;
}
