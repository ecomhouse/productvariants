<?php

namespace EcomHouse\ProductVariants\Block\Adminhtml\Group\Edit\Tab\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class GridImage extends AbstractRenderer
{
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * GridImage constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(Context $context, StoreManagerInterface $storeManager, array $data = [])
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($value) {
            $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $imageUrl = $mediaDirectory.'/catalog/product'. $value;

            return '<img src="' . $imageUrl . '" width="90"/>';
        }

        return '';
    }
}
