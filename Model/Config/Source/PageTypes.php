<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PageTypes implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'home', 'label' => __('Home Page')],
            ['value' => 'cms', 'label' => __('CMS Pages')],
            ['value' => 'category', 'label' => __('Category Pages')],
            ['value' => 'product', 'label' => __('Product Pages')],
            ['value' => 'cart', 'label' => __('Cart')],
            ['value' => 'checkout', 'label' => __('Checkout')],
            ['value' => 'search', 'label' => __('Search Results')],
            ['value' => 'account', 'label' => __('Customer Account')],
        ];
    }
}
