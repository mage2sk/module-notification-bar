<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Panth\NotificationBar\Model\BarFactory;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Panth_NotificationBar::manage';

    /**
     * Multi-select fields that need array-to-string conversion
     */
    private const MULTI_SELECT_FIELDS = [
        'store_ids',
        'customer_groups',
        'target_page_types',
        'target_countries',
    ];

    /**
     * @var BarFactory
     */
    private BarFactory $barFactory;

    /**
     * @var BarResource
     */
    private BarResource $barResource;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @param Context $context
     * @param BarFactory $barFactory
     * @param BarResource $barResource
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        BarFactory $barFactory,
        BarResource $barResource,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->barFactory = $barFactory;
        $this->barResource = $barResource;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Save notification bar
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $barId = isset($data['bar_id']) ? (int)$data['bar_id'] : null;

        try {
            $model = $this->barFactory->create();

            if ($barId) {
                $this->barResource->load($model, $barId);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This notification bar no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                unset($data['bar_id']);
            }

            $data = $this->prepareMultiSelectData($data);
            $model->setData(array_merge($model->getData(), $data));

            $this->barResource->save($model);
            $this->messageManager->addSuccessMessage(__('The notification bar has been saved.'));
            $this->dataPersistor->clear('panth_notification_bar');

            if ($this->getRequest()->getParam('back') === 'edit') {
                return $resultRedirect->setPath('*/*/edit', ['bar_id' => $model->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the notification bar.'));
        }

        $this->dataPersistor->set('panth_notification_bar', $data);

        if ($barId) {
            return $resultRedirect->setPath('*/*/edit', ['bar_id' => $barId]);
        }

        return $resultRedirect->setPath('*/*/new');
    }

    /**
     * Convert multi-select array values to comma-separated strings
     *
     * @param array $data
     * @return array
     */
    private function prepareMultiSelectData(array $data): array
    {
        foreach (self::MULTI_SELECT_FIELDS as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = implode(',', $data[$field]);
            }
        }

        return $data;
    }
}
