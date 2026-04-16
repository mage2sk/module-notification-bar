<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Block\Adminhtml\Bar\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData(): array
    {
        if (!$this->getBarId()) {
            return [];
        }

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\''
                . __('Are you sure you want to delete this notification bar?')
                . '\', \'' . $this->getDeleteUrl() . '\', {data: {}})',
            'sort_order' => 20,
        ];
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    private function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['bar_id' => $this->getBarId()]);
    }
}
