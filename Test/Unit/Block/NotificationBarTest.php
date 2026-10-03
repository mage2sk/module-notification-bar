<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Block;

use Panth\NotificationBar\Block\NotificationBar;
use Panth\NotificationBar\Model\Bar;
use PHPUnit\Framework\TestCase;

class NotificationBarTest extends TestCase
{
    public function testIdentitiesUseTheBarCacheTagSoSavingABarFlushesTheBlock(): void
    {
        $block = (new \ReflectionClass(NotificationBar::class))->newInstanceWithoutConstructor();

        $this->assertSame([Bar::CACHE_TAG], $block->getIdentities());
        $this->assertSame('panth_nbar', Bar::CACHE_TAG);
    }
}
