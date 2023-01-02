<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Controller\Adminhtml\Group;

use EcomHouse\ProductVariants\Api\Data\GroupInterfaceFactory;
use EcomHouse\ProductVariants\Api\GroupRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'EcomHouse_ProductVariants::Group_save';

    protected PageFactory $resultPageFactory;
    private GroupRepositoryInterface $groupRepository;
    private GroupInterfaceFactory $groupFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->groupRepository = $groupRepository;
        $this->groupFactory = $groupFactory;
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $id = $this->getRequest()->getParam('group_id');
        $group = $this->groupFactory->create();

        if ($id) {
            try {
                $group = $this->groupRepository->get((int)$id);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('This Group no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__('Groups'));
        $resultPage->getConfig()->getTitle()
            ->prepend($group->getGroupId() ? __('Edit Group') : __('New Group'));

        return $resultPage;
    }
}
