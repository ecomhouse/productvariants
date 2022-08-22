<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model;

use EcomHouse\ProductVariants\Api\Data\GroupInterface;
use EcomHouse\ProductVariants\Api\Data\GroupInterfaceFactory;
use EcomHouse\ProductVariants\Api\Data\GroupSearchResultsInterfaceFactory;
use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use EcomHouse\ProductVariants\Model\ResourceModel\Group as ResourceGroup;
use EcomHouse\ProductVariants\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class GroupRepository implements GroupRepositoryInterface
{
    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ResourceGroup
     */
    protected $resource;

    /**
     * @var Group
     */
    protected $searchResultsFactory;

    /**
     * @var GroupInterfaceFactory
     */
    protected $groupFactory;


    /**
     * @param ResourceGroup $resource
     * @param GroupInterfaceFactory $groupFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param GroupSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
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

    /**
     * @inheritDoc
     */
    public function save(GroupInterface $group)
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

    /**
     * @inheritDoc
     */
    public function get($groupId)
    {
        $group = $this->groupFactory->create();
        $this->resource->load($group, $groupId);
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('Group with id "%1" does not exist.', $groupId));
        }
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groupCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(GroupInterface $group)
    {
        try {
            $groupModel = $this->groupFactory->create();
            $this->resource->load($groupModel, $group->getGroupId());
            $this->resource->delete($groupModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Group: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($groupId)
    {
        return $this->delete($this->get($groupId));
    }
}

