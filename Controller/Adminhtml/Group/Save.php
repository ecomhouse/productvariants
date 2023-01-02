<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Controller\Adminhtml\Group;

use EcomHouse\ProductVariants\Api\Data\GroupInterfaceFactory;
use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    private GroupInterfaceFactory $groupInterfaceFactory;
    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        Context $context,
        GroupInterfaceFactory $groupInterfaceFactory,
        GroupRepositoryInterface $groupRepository
    ) {
        parent::__construct($context);
        $this->groupInterfaceFactory = $groupInterfaceFactory;
        $this->groupRepository = $groupRepository;
    }

    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('group_id');
            $group = $this->groupInterfaceFactory->create();

            if ($id) {
                try {
                    $group = $this->groupRepository->get((int)$id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This Group no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $data['product_ids'] = [];
            $assignedProducts = $this->getRequest()->getParam('products');
            if ($assignedProducts) {
                $data['product_ids'] = array_column($assignedProducts['assign_products_grid'], 'entity_id');
            }

            $group->setData($data);

            try {
                $this->groupRepository->save($group);
                $this->messageManager->addSuccessMessage(__('You saved the Group.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['group_id' => $group->getGroupId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Group.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['group_id' => $this->getRequest()->getParam('group_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
