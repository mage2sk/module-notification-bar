<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BackgroundType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'color', 'label' => __('Color')],
            ['value' => 'gradient', 'label' => __('Gradient')],
            ['value' => 'image', 'label' => __('Image')],
        ];
    }
}
