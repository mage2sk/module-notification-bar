<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Position implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'top_fixed', 'label' => __('Top Fixed')],
            ['value' => 'top_static', 'label' => __('Top Static')],
            ['value' => 'bottom_fixed', 'label' => __('Bottom Fixed')],
            ['value' => 'bottom_floating', 'label' => __('Bottom Floating')],
        ];
    }
}
