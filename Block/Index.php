<?php

namespace EcomHouse\ProductVariants\Block;

use EcomHouse\ProductVariants\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Index extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, Data $helper, array $data = [])
    {
        $this->_registry = $registry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed|null
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionsVariantsArray()
    {
        return $this->helper->getOptionsVariantsCollection($this->getProduct());
    }

}
