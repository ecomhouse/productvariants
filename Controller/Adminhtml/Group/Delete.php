<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Controller\Adminhtml\Group;

use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'EcomHouse_ProductVariants::Group_delete';

    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        Context $context,
        GroupRepositoryInterface $groupRepository
    ) {
        parent::__construct($context);
        $this->groupRepository = $groupRepository;
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('group_id');
        if ($id) {
            try {
                $this->groupRepository->deleteById((int) $id);

                $this->messageManager->addSuccessMessage(__('You deleted the Group.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['group_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Group to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
