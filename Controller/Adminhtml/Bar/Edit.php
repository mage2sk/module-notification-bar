<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Panth\NotificationBar\Model\BarFactory;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Panth_NotificationBar::manage';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

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
     * @param PageFactory $resultPageFactory
     * @param BarFactory $barFactory
     * @param BarResource $barResource
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BarFactory $barFactory,
        BarResource $barResource
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->barFactory = $barFactory;
        $this->barResource = $barResource;
    }

    /**
     * Edit or create notification bar page
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $barId = (int)$this->getRequest()->getParam('bar_id');

        if ($barId) {
            $bar = $this->barFactory->create();
            $this->barResource->load($bar, $barId);

            if (!$bar->getId()) {
                $this->messageManager->addErrorMessage(__('This notification bar no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Panth_NotificationBar::manage_bars');

        $title = $barId
            ? __('Edit: %1', $bar->getData('name'))
            : __('New Bar');

        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
