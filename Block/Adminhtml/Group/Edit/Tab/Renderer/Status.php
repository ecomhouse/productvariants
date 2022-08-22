<?php

namespace EcomHouse\ProductVariants\Block\Adminhtml\Group\Edit\Tab\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Status extends AbstractRenderer
{
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($value == 1) {
            return __('Enabled');
        } else {
            return __('Disabled');
        }
    }
}
