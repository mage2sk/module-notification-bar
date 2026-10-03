<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Panth\NotificationBar\Model\Bar;

class NotificationBar extends Template implements IdentityInterface
{
    public function getIdentities(): array
    {
        return [Bar::CACHE_TAG];
    }
}
