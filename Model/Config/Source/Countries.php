<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Model\Config\Source;

use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Data\OptionSourceInterface;

class Countries implements OptionSourceInterface
{
    private Country $countrySource;

    public function __construct(Country $countrySource)
    {
        $this->countrySource = $countrySource;
    }

    public function toOptionArray(): array
    {
        $options = $this->countrySource->toOptionArray(true);

        return array_filter($options, fn($opt) => !empty($opt['value']));
    }
}
