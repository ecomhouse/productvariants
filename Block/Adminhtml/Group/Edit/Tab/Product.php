<?php

namespace EcomHouse\ProductVariants\Block\Adminhtml\Group\Edit\Tab;

use EcomHouse\ProductVariants\Block\Adminhtml\Group\Edit\Tab\Renderer\GridImage;
use EcomHouse\ProductVariants\Block\Adminhtml\Group\Edit\Tab\Renderer\Status;
use EcomHouse\ProductVariants\Helper\Data as Helper;
use EcomHouse\ProductVariants\Model\Service\ProductVariantsService;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Registry;

class Product extends ExtendedGrid implements TabInterface
{
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductVariantsService
     */
    protected $groupService;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param Registry $coreRegistry
     * @param ProductVariantsService $groupService
     * @param CollectionFactory $productCollectionFactory
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context                $context,
        Data                   $backendHelper,
        Registry               $coreRegistry,
        ProductVariantsService $groupService,
        CollectionFactory      $productCollectionFactory,
        Helper                 $helper,
        array                  $data = []
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->groupService = $groupService;
        $this->helper = $helper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_variants_edit_tab_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setTitle(__('Select products'));
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($groupId = $this->getRequest()->getParam('group_id')) {
            $this->setDefaultFilter(['group_id' => $groupId]);
        }
        if ($this->canShowTab()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Select products');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Select products');
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect("*");

        if ($this->helper->isOnlySimpleProducts()) {
            $collection->addAttributeToFilter('type_id', Type::TYPE_SIMPLE);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', [
            'type' => 'checkbox',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id',
            'header_css_class' => 'col-select',
            'column_css_class' => 'col-select'
        ]);

        $this->addColumn('entity_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('image', [
            'header' => __('Image'),
            'index' => 'image',
            'header_css_class' => 'col-image',
            'column_css_class' => 'col-image',
            'sortable' => false,
            'filter' => false,
            'renderer' => GridImage::class
        ]);

        $this->addColumn('name', [
            'header' => __('Name'),
            'index' => 'name',
            'class' => 'xxx',
            'width' => '50px',
        ]);

        $this->addColumn('sku', [
            'header' => __('SKU'),
            'index' => 'sku',
            'class' => 'xxx',
            'width' => '50px',
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'index' => 'status',
            'class' => 'xxx',
            'width' => '50px',
            'renderer' => Status::class
        ]);

        $this->addColumn('price', [
            'header' => __('Price'),
            'type' => 'currency',
            'index' => 'price',
            'width' => '50px',
        ]);


        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this|Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return $this->coreRegistry->registry('ecomhouse_productvariants_group');
    }

    /**
     * Tab should be loaded through Ajax call
     * @return bool
     */
    public function isAjaxLoaded(): bool
    {
        return false;
    }

    /**
     * Checks when this block is readonly
     * @return bool
     */
    public function isReadonly(): bool
    {
        return false;
    }

    /**
     * Retrieve selected related products
     * @return array
     */
    public function _getSelectedProducts(): array
    {
        return array_keys($this->getSelectedProducts());
    }

    /**
     * @return array
     */
    public function getSelectedProducts(): array
    {
        $products = [];
        if ($groupId = $this->getRequest()->getParam('group_id')) {
            foreach ($this->groupService->getProductIds($groupId) as $item) {
                $products[(int)$item] = ['position' => 0];
            }
        }
        return $products;
    }

    /**
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('*/*/productGrid', ['_current' => true]);
    }
}
