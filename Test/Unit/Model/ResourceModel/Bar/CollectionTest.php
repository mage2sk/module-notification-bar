<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Model\ResourceModel\Bar;

use Panth\NotificationBar\Model\ResourceModel\Bar\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var array
     */
    private array $filters = [];

    private function collection(): Collection
    {
        $this->filters = [];
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFieldToFilter'])
            ->getMock();
        $collection->expects($this->atLeastOnce())->method('addFieldToFilter')->willReturnCallback(
            function ($field, $condition = null) use ($collection) {
                $this->filters[] = [$field, $condition];
                return $collection;
            }
        );

        return $collection;
    }

    public function testActiveFilterRequiresActiveFlagAndOpenDateWindow(): void
    {
        $collection = $this->collection();

        $this->assertSame($collection, $collection->addActiveFilter());
        $this->assertCount(3, $this->filters);
        $this->assertSame(['is_active', 1], $this->filters[0]);

        [$fromField, $fromCond] = $this->filters[1];
        $this->assertSame('date_from', $fromField);
        $this->assertSame(['null' => true], $fromCond[0]);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fromCond[1]['lteq']);

        [$toField, $toCond] = $this->filters[2];
        $this->assertSame('date_to', $toField);
        $this->assertSame(['null' => true], $toCond[0]);
        $this->assertSame($fromCond[1]['lteq'], $toCond[1]['gteq']);
    }

    public function testStoreFilterIncludesAllStoreViews(): void
    {
        $collection = $this->collection();

        $this->assertSame($collection, $collection->addStoreFilter(3));
        $this->assertSame(
            [['store_ids', [['finset' => '0'], ['finset' => '3']]]],
            $this->filters
        );
    }

    public function testPositionFilter(): void
    {
        $collection = $this->collection();

        $this->assertSame($collection, $collection->addPositionFilter('bottom_fixed'));
        $this->assertSame([['position', 'bottom_fixed']], $this->filters);
    }
}
