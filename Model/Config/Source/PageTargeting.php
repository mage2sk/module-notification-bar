<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PageTargeting implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'all', 'label' => __('All Pages')],
            ['value' => 'specific', 'label' => __('Specific Pages Only')],
            ['value' => 'exclude', 'label' => __('All Pages Except')],
        ];
    }
}
