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
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \EcomHouse\ProductVariants\Group\Api\Data\GroupInterface
     */
    public function setGroupId($groupId);

    /**
     * Get group_name
     * @return string|null
     */
    public function getGroupName();

    /**
     * Set group_name
     * @param string $groupName
     * @return \EcomHouse\ProductVariants\Group\Api\Data\GroupInterface
     */
    public function setGroupName($groupName);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \EcomHouse\ProductVariants\Group\Api\Data\GroupInterface
     */
    public function setStatus($status);
}

