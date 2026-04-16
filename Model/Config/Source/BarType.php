<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BarType implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'info', 'label' => __('Info')],
            ['value' => 'warning', 'label' => __('Warning')],
            ['value' => 'success', 'label' => __('Success')],
            ['value' => 'promo', 'label' => __('Promo')],
            ['value' => 'urgent', 'label' => __('Urgent')],
            ['value' => 'custom', 'label' => __('Custom')],
        ];
    }
}
