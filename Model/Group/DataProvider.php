<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model\Group;

use EcomHouse\ProductVariants\Model\ResourceModel\Group\CollectionFactory;
use EcomHouse\ProductVariants\Model\Service\ProductVariantsService;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @inheritDoc
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var ProductVariantsService
     */
    protected $productVariantsService;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        ProductVariantsService $productVariantsService,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->productVariantsService = $productVariantsService;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $itemData = $model->getData();
            $itemData['attributes_ids'] = $this->getAttributesIds((int)$model->getId());
            $this->loadedData[$model->getId()]['general'] = $itemData;
        }
        $data = $this->dataPersistor->get('ecomhouse_productvariants_group');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()]['general'] = $model->getData();
            $this->dataPersistor->clear('ecomhouse_productvariants_group');
        }

        return $this->loadedData;
    }

    /**
     * @param int $id
     * @return array
     */
    private function getAttributesIds(int $id)
    {
        $ids = [];
        $attributes = $this->productVariantsService->getOptionsIds($id);
        foreach ($attributes as $attribute) {
            $ids[] = (string)$attribute;
        }
        return $ids;
    }

}

