<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Panth\NotificationBar\Model\BarFactory;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class InlineEdit extends Action implements HttpPostActionInterface
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
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @param Context $context
     * @param BarFactory $barFactory
     * @param BarResource $barResource
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        BarFactory $barFactory,
        BarResource $barResource,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->barFactory = $barFactory;
        $this->barResource = $barResource;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Process inline edit
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $items = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($items))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach ($items as $barId => $barData) {
            try {
                $bar = $this->barFactory->create();
                $this->barResource->load($bar, (int)$barId);
                $bar->setData(array_merge($bar->getData(), $barData));
                $this->barResource->save($bar);
            } catch (LocalizedException $e) {
                $messages[] = __('[Bar ID: %1] %2', $barId, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = __('[Bar ID: %1] Something went wrong while saving.', $barId);
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }
}
