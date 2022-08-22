<?php

namespace EcomHouse\ProductVariants\Controller\Adminhtml\Group;

use EcomHouse\ProductVariants\Block\Adminhtml\Group\Edit\Tab\Product;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite as RewriteAction;

class ProductGrid extends RewriteAction implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Ajax products grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(Product::class)->toHtml()
        );
    }
}

