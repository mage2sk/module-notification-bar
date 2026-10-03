<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Model\Bar;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Registry;
use Panth\NotificationBar\Model\Bar;
use Panth\NotificationBar\Model\Bar\DataProvider;
use Panth\NotificationBar\Model\ResourceModel\Bar\Collection;
use Panth\NotificationBar\Model\ResourceModel\Bar\CollectionFactory;
use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    private function bar(array $data = []): Bar
    {
        $resource = $this->createStub(AbstractDb::class);
        $resource->method('getIdFieldName')->willReturn('bar_id');

        $bar = new Bar($this->createStub(Context::class), $this->createStub(Registry::class), $resource);
        $bar->setData($data);

        return $bar;
    }

    private function provider(array $bars, DataPersistorInterface $persistor): DataProvider
    {
        $collection = $this->createStub(Collection::class);
        $collection->method('getItems')->willReturn($bars);
        $collection->method('getNewEmptyItem')->willReturnCallback(fn() => $this->bar());
        $factory = $this->createStub(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);

        return new DataProvider('bar_form_data_source', 'bar_id', 'bar_id', $factory, $persistor);
    }

    public function testLoadsBarsKeyedByIdAndSplitsMultiValueFields(): void
    {
        $persistor = $this->createStub(DataPersistorInterface::class);
        $persistor->method('get')->willReturn(null);

        $data = $this->provider([
            $this->bar([
                'bar_id' => 3,
                'name' => 'Promo',
                'store_ids' => '1,2',
                'customer_groups' => '0,1',
                'target_page_types' => 'home',
                'target_countries' => '',
                'target_urls' => 'a,b',
            ]),
        ], $persistor)->getData();

        $this->assertSame([3], array_keys($data));
        $this->assertSame(['1', '2'], $data[3]['store_ids']);
        $this->assertSame(['0', '1'], $data[3]['customer_groups']);
        $this->assertSame(['home'], $data[3]['target_page_types']);
        $this->assertSame('', $data[3]['target_countries']);
        $this->assertSame('a,b', $data[3]['target_urls']);
        $this->assertSame('Promo', $data[3]['name']);
    }

    public function testPersistedDataOverridesAndIsClearedOnce(): void
    {
        $persistor = $this->createMock(DataPersistorInterface::class);
        $persistor->method('get')->with('panth_notification_bar')->willReturn([
            'bar_id' => 3,
            'name' => 'Unsaved edit',
            'store_ids' => '4',
        ]);
        $persistor->expects($this->once())->method('clear')->with('panth_notification_bar');

        $provider = $this->provider([$this->bar(['bar_id' => 3, 'name' => 'Stored'])], $persistor);
        $data = $provider->getData();

        $this->assertSame('Unsaved edit', $data[3]['name']);
        $this->assertSame(['4'], $data[3]['store_ids']);
        $this->assertSame($data, $provider->getData());
    }

    public function testEmptyCollectionWithoutPersistedDataReturnsEmptyArray(): void
    {
        $persistor = $this->createMock(DataPersistorInterface::class);
        $persistor->method('get')->willReturn([]);
        $persistor->expects($this->never())->method('clear');

        $this->assertSame([], $this->provider([], $persistor)->getData());
    }
}
