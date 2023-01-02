<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model;

use EcomHouse\ProductVariants\Api\Data\GroupInterface;
use EcomHouse\ProductVariants\Api\Data\GroupInterfaceFactory;
use EcomHouse\ProductVariants\Api\Data\GroupSearchResultsInterface;
use EcomHouse\ProductVariants\Api\Data\GroupSearchResultsInterfaceFactory;
use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use EcomHouse\ProductVariants\Model\ResourceModel\Group as ResourceGroup;
use EcomHouse\ProductVariants\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class GroupRepository implements GroupRepositoryInterface
{
    protected GroupCollectionFactory $groupCollectionFactory;
    protected CollectionProcessorInterface $collectionProcessor;
    protected ResourceGroup $resource;
    protected GroupSearchResultsInterfaceFactory $searchResultsFactory;
    protected GroupInterfaceFactory $groupFactory;

    public function __construct(
        ResourceGroup $resource,
        GroupInterfaceFactory $groupFactory,
        GroupCollectionFactory $groupCollectionFactory,
        GroupSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->groupFactory = $groupFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(GroupInterface $group): GroupInterface
    {
        try {
            $this->resource->save($group);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the group: %1',
                $exception->getMessage()
            ));
        }
        return $group;
    }

    public function get(int $groupId): GroupInterface
    {
        $group = $this->groupFactory->create();
        $this->resource->load($group, $groupId);
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('Group with id "%1" does not exist.', $groupId));
        }
        return $group;
    }

    public function getList(
        SearchCriteriaInterface $criteria
    ): GroupSearchResultsInterface {
        $collection = $this->groupCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var GroupInterface[] $items */
        $items = $collection->getItems();

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function delete(GroupInterface $group): bool
    {
        try {
            $this->resource->delete($group);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Group: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById(int $groupId): bool
    {
        return $this->delete($this->get($groupId));
    }
}
