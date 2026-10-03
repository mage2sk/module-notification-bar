<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Ui\Component\Listing;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Panth\NotificationBar\Ui\Component\Listing\LikeFulltextFilter;
use PHPUnit\Framework\TestCase;

class LikeFulltextFilterTest extends TestCase
{
    private function filter($value): Filter
    {
        $filter = $this->createStub(Filter::class);
        $filter->method('getValue')->willReturn($value);

        return $filter;
    }

    private function collection(Select $select): AbstractDb
    {
        $connection = $this->createStub(AdapterInterface::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn($c) => '`' . $c . '`');
        $connection->method('quoteInto')->willReturnCallback(
            static fn($text, $value) => str_replace('?', "'" . $value . "'", $text)
        );
        $collection = $this->createStub(AbstractDb::class);
        $collection->method('getConnection')->willReturn($connection);
        $collection->method('getSelect')->willReturn($select);

        return $collection;
    }

    public function testBuildsOrLikeConditionAcrossConfiguredColumns(): void
    {
        $select = $this->createMock(Select::class);
        $select->expects($this->once())->method('where')->with("`name` LIKE '%promo%' OR `content` LIKE '%promo%'");

        (new LikeFulltextFilter(['name', 'content']))->apply($this->collection($select), $this->filter('  promo '));
    }

    public function testEscapesLikeWildcards(): void
    {
        $select = $this->createMock(Select::class);
        $select->expects($this->once())->method('where')->with("`name` LIKE '%50\\%\\_off%'");

        (new LikeFulltextFilter(['name']))->apply($this->collection($select), $this->filter('50%_off'));
    }

    public function testTruncatesLongSearchTerms(): void
    {
        $captured = null;
        $select = $this->createStub(Select::class);
        $select->method('where')->willReturnCallback(function ($cond) use (&$captured, $select) {
            $captured = $cond;
            return $select;
        });

        (new LikeFulltextFilter(['name']))->apply($this->collection($select), $this->filter(str_repeat('a', 300)));

        $this->assertSame("`name` LIKE '%" . str_repeat('a', 200) . "%'", $captured);
    }

    public function testNonStringColumnsAreIgnored(): void
    {
        $select = $this->createMock(Select::class);
        $select->expects($this->once())->method('where')->with("`name` LIKE '%x%'");

        (new LikeFulltextFilter([5, 'name', null]))->apply($this->collection($select), $this->filter('x'));
    }

    public function testBlankOrNonScalarValuesAreIgnored(): void
    {
        $select = $this->createMock(Select::class);
        $select->expects($this->never())->method('where');
        $collection = $this->collection($select);
        $filter = new LikeFulltextFilter(['name']);

        $filter->apply($collection, $this->filter('   '));
        $filter->apply($collection, $this->filter(['a']));
        $filter->apply($collection, $this->filter(null));
    }

    public function testNoColumnsMeansNoFilter(): void
    {
        $select = $this->createMock(Select::class);
        $select->expects($this->never())->method('where');

        (new LikeFulltextFilter([]))->apply($this->collection($select), $this->filter('x'));
    }

    public function testNonDbCollectionIsIgnored(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->never())->method('addFieldToFilter');

        (new LikeFulltextFilter(['name']))->apply($collection, $this->filter('x'));
    }
}
