<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Animation implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'slide_down', 'label' => __('Slide Down')],
            ['value' => 'fade_in', 'label' => __('Fade In')],
            ['value' => 'none', 'label' => __('None')],
        ];
    }
}
