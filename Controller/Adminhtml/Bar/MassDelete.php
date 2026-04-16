<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;
use Panth\NotificationBar\Model\ResourceModel\Bar\CollectionFactory;

class MassDelete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Panth_NotificationBar::manage';

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var BarResource
     */
    private BarResource $barResource;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param BarResource $barResource
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BarResource $barResource
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->barResource = $barResource;
    }

    /**
     * Mass delete notification bars
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }

        $deleted = 0;
        $errors = 0;

        foreach ($collection as $bar) {
            try {
                $this->barResource->delete($bar);
                $deleted++;
            } catch (\Exception $e) {
                $errors++;
            }
        }

        if ($deleted) {
            $this->messageManager->addSuccessMessage(
                $deleted === 1
                    ? __('1 notification bar has been deleted.')
                    : __('%1 notification bars have been deleted.', $deleted)
            );
        }

        if ($errors) {
            $this->messageManager->addErrorMessage(
                $errors === 1
                    ? __('1 notification bar could not be deleted.')
                    : __('%1 notification bars could not be deleted.', $errors)
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
