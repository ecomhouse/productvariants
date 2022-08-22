<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Controller\Adminhtml\Group;

use EcomHouse\ProductVariants\Model\Service\ProductVariantsService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var ProductVariantsService
     */
    protected $productVariantsService;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ProductVariantsService $productVariantsService
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ProductVariantsService $productVariantsService
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->productVariantsService = $productVariantsService;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue('general');
        if ($data) {
            $id = $this->getRequest()->getParam('group_id');

            $model = $this->_objectManager->create(\EcomHouse\ProductVariants\Model\Group::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Group no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $attributesIds = $data['attributes_ids'] ?? [];
                $this->productVariantsService->saveOptionsRelation($model, $attributesIds);
                $this->saveProductsRelation($model);
                $this->messageManager->addSuccessMessage(__('You saved the Group.'));
                $this->dataPersistor->clear('ecomhouse_productvariants_group');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['group_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Group.'));
            }

            $this->dataPersistor->set('ecomhouse_productvariants_group', $data);
            return $resultRedirect->setPath('*/*/edit', ['group_id' => $this->getRequest()->getParam('group_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $model
     */
    protected function saveProductsRelation($model)
    {
        $data = $this->getRequest()->getPostValue('product');
        $productsIds = str_replace("&on", "",  $data['list']);
        $productsIds = str_replace("on", "",  $productsIds);
        if ($productsIds) {
            $productsIds = explode("&", $productsIds);
        } else {
            $productsIds = [];
        }

        $this->productVariantsService->saveProductsRelation($model, $productsIds);
    }

}

