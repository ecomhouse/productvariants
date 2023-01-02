<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Ui\DataProvider;

use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use EcomHouse\ProductVariants\Model\ResourceModel\Group\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class Group extends AbstractDataProvider
{
    private GroupRepositoryInterface $groupRepository;
    private PoolInterface $modifiersPool;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        GroupRepositoryInterface $groupRepository,
        PoolInterface $modifiersPool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->groupRepository = $groupRepository;
        $this->modifiersPool = $modifiersPool;
    }

    public function getData(): array
    {
        foreach ($this->collection->getAllIds() as $groupId) {
            $group = $this->groupRepository->get((int) $groupId);
            $this->data[$groupId] = $group->getData();

            foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
                $this->data = $modifier->modifyData($this->data, (int) $groupId);
            }
        }

        return $this->data;
    }
}
