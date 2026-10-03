<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Registry;
use Panth\NotificationBar\Model\Bar;
use PHPUnit\Framework\TestCase;

class BarTest extends TestCase
{
    private function bar(array $data = []): Bar
    {
        $resource = $this->createStub(AbstractDb::class);
        $resource->method('getIdFieldName')->willReturn('bar_id');

        $bar = new Bar($this->createStub(Context::class), $this->createStub(Registry::class), $resource);
        $bar->setData($data);

        return $bar;
    }

    public function testIdentitiesForNewBarContainOnlyTheBaseTag(): void
    {
        $this->assertSame([Bar::CACHE_TAG], $this->bar()->getIdentities());
    }

    public function testIdentitiesForSavedBarIncludeTheBarTag(): void
    {
        $this->assertSame(['panth_nbar', 'panth_nbar_12'], $this->bar(['bar_id' => 12])->getIdentities());
    }

    public function testNumericGettersCastAndKeepNull(): void
    {
        $bar = $this->bar([
            'bar_id' => '4',
            'sort_order' => '10',
            'font_size' => '15',
            'bar_height' => '0',
            'cookie_duration' => '30',
            'auto_close_seconds' => '8',
        ]);

        $this->assertSame(4, $bar->getBarId());
        $this->assertSame(10, $bar->getSortOrder());
        $this->assertSame(15, $bar->getFontSize());
        $this->assertSame(0, $bar->getBarHeight());
        $this->assertSame(30, $bar->getCookieDuration());
        $this->assertSame(8, $bar->getAutoCloseSeconds());

        $empty = $this->bar();
        $this->assertNull($empty->getBarId());
        $this->assertNull($empty->getSortOrder());
        $this->assertNull($empty->getFontSize());
        $this->assertNull($empty->getCookieDuration());
        $this->assertNull($empty->getAutoCloseSeconds());
    }

    public function testBooleanGettersCast(): void
    {
        $bar = $this->bar([
            'is_active' => '1',
            'cta_enabled' => '0',
            'cta_open_new_tab' => 1,
            'countdown_enabled' => null,
            'is_dismissible' => '1',
            'show_on_mobile' => '0',
            'show_on_desktop' => '1',
        ]);

        $this->assertTrue($bar->getIsActive());
        $this->assertFalse($bar->getCtaEnabled());
        $this->assertTrue($bar->getCtaOpenNewTab());
        $this->assertFalse($bar->getCountdownEnabled());
        $this->assertTrue($bar->getIsDismissible());
        $this->assertFalse($bar->getShowOnMobile());
        $this->assertTrue($bar->getShowOnDesktop());
    }

    public function testSettersWriteUnderlyingColumns(): void
    {
        $bar = $this->bar()
            ->setName('Promo')
            ->setIsActive(true)
            ->setPosition('bottom_fixed')
            ->setStoreIds('1,2')
            ->setTargetUrls('sale/*');

        $this->assertSame('Promo', $bar->getData('name'));
        $this->assertTrue($bar->getData('is_active'));
        $this->assertSame('bottom_fixed', $bar->getData('position'));
        $this->assertSame('1,2', $bar->getData('store_ids'));
        $this->assertSame('sale/*', $bar->getData('target_urls'));
    }

    public function testArrayGettersSplitCommaSeparatedValues(): void
    {
        $bar = $this->bar([
            'store_ids' => '1,2,',
            'customer_groups' => '1,3',
            'target_countries' => 'US,GB',
            'target_page_types' => 'home,cart',
            'target_urls' => 'sale/*,about',
        ]);

        $this->assertSame(['1', '2'], array_values($bar->getStoreIdsArray()));
        $this->assertSame(['1', '3'], $bar->getCustomerGroupsArray());
        $this->assertSame(['US', 'GB'], $bar->getTargetCountriesArray());
        $this->assertSame(['home', 'cart'], $bar->getTargetPageTypesArray());
        $this->assertSame(['sale/*', 'about'], $bar->getTargetUrlsArray());
    }

    public function testArrayGettersReturnEmptyArrayWhenUnset(): void
    {
        $bar = $this->bar(['store_ids' => '', 'customer_groups' => null]);

        $this->assertSame([], $bar->getStoreIdsArray());
        $this->assertSame([], $bar->getCustomerGroupsArray());
        $this->assertSame([], $bar->getTargetCountriesArray());
        $this->assertSame([], $bar->getTargetPageTypesArray());
        $this->assertSame([], $bar->getTargetUrlsArray());
    }
}
