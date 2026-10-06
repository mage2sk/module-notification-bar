<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\Collection as GroupCollection;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Directory\Model\Config\Source\Country;
use Panth\NotificationBar\Model\Config\Source\Animation;
use Panth\NotificationBar\Model\Config\Source\BackgroundType;
use Panth\NotificationBar\Model\Config\Source\BarType;
use Panth\NotificationBar\Model\Config\Source\Countries;
use Panth\NotificationBar\Model\Config\Source\CustomerGroups;
use Panth\NotificationBar\Model\Config\Source\PageTargeting;
use Panth\NotificationBar\Model\Config\Source\PageTypes;
use Panth\NotificationBar\Model\Config\Source\Position;
use PHPUnit\Framework\TestCase;

class SourceModelsTest extends TestCase
{
    private function values(array $options): array
    {
        return array_column($options, 'value');
    }

    public function testAnimationValuesMatchTheViewModelWhitelist(): void
    {
        $this->assertSame(['slide_down', 'fade_in', 'none'], $this->values((new Animation())->toOptionArray()));
    }

    public function testPositionValuesMatchTheViewModelWhitelist(): void
    {
        $this->assertSame(
            ['top_fixed', 'top_static', 'bottom_fixed', 'bottom_floating'],
            $this->values((new Position())->toOptionArray())
        );
    }

    public function testBackgroundTypesMatchTheViewModel(): void
    {
        $this->assertSame(['color', 'gradient', 'image'], $this->values((new BackgroundType())->toOptionArray()));
    }

    public function testBarTypes(): void
    {
        $this->assertSame(
            ['info', 'warning', 'success', 'promo', 'urgent', 'custom'],
            $this->values((new BarType())->toOptionArray())
        );
    }

    public function testPageTargetingModes(): void
    {
        $this->assertSame(['all', 'specific', 'exclude'], $this->values((new PageTargeting())->toOptionArray()));
    }

    public function testPageTypesAreAllUnderstoodByTheMatcher(): void
    {
        $this->assertSame(
            ['home', 'cms', 'category', 'product', 'cart', 'checkout', 'search', 'account'],
            $this->values((new PageTypes())->toOptionArray())
        );
    }

    public function testEveryStaticOptionHasALabel(): void
    {
        $sources = [
            new Animation(),
            new BackgroundType(),
            new Position(),
            new BarType(),
            new PageTargeting(),
            new PageTypes(),
        ];
        foreach ($sources as $source) {
            foreach ($source->toOptionArray() as $option) {
                $this->assertNotSame('', (string)$option['label'], get_class($source));
            }
        }
    }

    public function testCountriesDropTheEmptyPlaceholder(): void
    {
        $country = $this->createMock(Country::class);
        $country->expects($this->once())->method('toOptionArray')->with(true)->willReturn([
            ['value' => '', 'label' => ' '],
            ['value' => 'US', 'label' => 'United States'],
            ['value' => 'GB', 'label' => 'United Kingdom'],
        ]);

        $this->assertSame(['US', 'GB'], array_values($this->values((new Countries($country))->toOptionArray())));
    }

    public function testCustomerGroupsAreLoadedOnce(): void
    {
        $options = [['value' => 0, 'label' => 'NOT LOGGED IN'], ['value' => 1, 'label' => 'General']];
        $collection = $this->createStub(GroupCollection::class);
        $collection->method('toOptionArray')->willReturn($options);
        $factory = $this->createMock(GroupCollectionFactory::class);
        $factory->expects($this->once())->method('create')->willReturn($collection);

        $source = new CustomerGroups($factory);

        $this->assertSame($options, $source->toOptionArray());
        $this->assertSame($options, $source->toOptionArray());
    }
}
