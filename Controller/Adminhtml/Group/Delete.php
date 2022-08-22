<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Controller\Adminhtml\Group;

use EcomHouse\ProductVariants\Controller\Adminhtml\Group;

class Delete extends Group
{
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('group_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\EcomHouse\ProductVariants\Model\Group::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Group.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['group_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Group to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

