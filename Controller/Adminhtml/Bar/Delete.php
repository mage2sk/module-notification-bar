<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Panth\NotificationBar\Model\BarFactory;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Delete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Panth_NotificationBar::manage';

    /**
     * @var BarFactory
     */
    private BarFactory $barFactory;

    /**
     * @var BarResource
     */
    private BarResource $barResource;

    /**
     * @param Context $context
     * @param BarFactory $barFactory
     * @param BarResource $barResource
     */
    public function __construct(
        Context $context,
        BarFactory $barFactory,
        BarResource $barResource
    ) {
        parent::__construct($context);
        $this->barFactory = $barFactory;
        $this->barResource = $barResource;
    }

    /**
     * Delete notification bar
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $barId = (int)$this->getRequest()->getParam('bar_id');

        if (!$barId) {
            $this->messageManager->addErrorMessage(__('We cannot find a notification bar to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model = $this->barFactory->create();
            $this->barResource->load($model, $barId);
            $this->barResource->delete($model);
            $this->messageManager->addSuccessMessage(__('The notification bar has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the notification bar.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
