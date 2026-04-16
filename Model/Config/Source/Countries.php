<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Data\OptionSourceInterface;

class Countries implements OptionSourceInterface
{
    /**
     * @var Country
     */
    private Country $countrySource;

    /**
     * @param Country $countrySource
     */
    public function __construct(Country $countrySource)
    {
        $this->countrySource = $countrySource;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $options = $this->countrySource->toOptionArray(true);
        // Remove the empty "Please select" option - not needed for multiselect
        return array_filter($options, fn($opt) => !empty($opt['value']));
    }
}
