<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Controller\Adminhtml\Bar;

use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Panth\NotificationBar\Controller\Adminhtml\Bar\MassDelete;
use Panth\NotificationBar\Controller\Adminhtml\Bar\MassStatus;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;
use Panth\NotificationBar\Model\ResourceModel\Bar\Collection;
use Panth\NotificationBar\Model\ResourceModel\Bar\CollectionFactory;

class MassActionsTest extends ControllerTestCase
{
    private function filter(array $bars): Filter
    {
        $collection = $this->createStub(Collection::class);
        $collection->method('getIterator')->willReturnCallback(static fn() => new \ArrayIterator($bars));
        $filter = $this->createStub(Filter::class);
        $filter->method('getCollection')->willReturn($collection);

        return $filter;
    }

    private function failingFilter(): Filter
    {
        $filter = $this->createStub(Filter::class);
        $filter->method('getCollection')->willThrowException(new LocalizedException(__('Nothing selected')));

        return $filter;
    }

    private function factory(): CollectionFactory
    {
        $factory = $this->createStub(CollectionFactory::class);
        $factory->method('create')->willReturn($this->createStub(Collection::class));

        return $factory;
    }

    public function testMassDeleteSingleBar(): void
    {
        $bar = $this->bar(['bar_id' => 1]);
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->once())->method('delete')->with($bar);

        (new MassDelete($this->buildContext(), $this->filter([$bar]), $this->factory(), $resource))->execute();

        $this->assertSame(['1 notification bar has been deleted.'], $this->messages['success']);
        $this->assertSame([], $this->messages['error']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }

    public function testMassDeleteCountsSuccessesAndFailures(): void
    {
        $bars = [
            $this->bar(['bar_id' => 1]),
            $this->bar(['bar_id' => 2]),
            $this->bar(['bar_id' => 3]),
            $this->bar(['bar_id' => 4]),
        ];
        $resource = $this->createStub(BarResource::class);
        $resource->method('delete')->willReturnCallback(function ($bar) use ($resource) {
            if ((int)$bar->getId() > 2) {
                throw new \RuntimeException('fk');
            }
            return $resource;
        });

        (new MassDelete($this->buildContext(), $this->filter($bars), $this->factory(), $resource))->execute();

        $this->assertSame(['2 notification bars have been deleted.'], $this->messages['success']);
        $this->assertSame(['2 notification bars could not be deleted.'], $this->messages['error']);
    }

    public function testMassDeleteSingleFailure(): void
    {
        $resource = $this->createStub(BarResource::class);
        $resource->method('delete')->willThrowException(new \RuntimeException('x'));

        (new MassDelete($this->buildContext(), $this->filter([$this->bar()]), $this->factory(), $resource))
            ->execute();

        $this->assertSame([], $this->messages['success']);
        $this->assertSame(['1 notification bar could not be deleted.'], $this->messages['error']);
    }

    public function testMassDeleteFilterErrorRedirects(): void
    {
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->never())->method('delete');

        (new MassDelete($this->buildContext(), $this->failingFilter(), $this->factory(), $resource))->execute();

        $this->assertSame(['Nothing selected'], $this->messages['error']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }

    public function testMassEnableSetsStatusOnEveryBar(): void
    {
        $bars = [$this->bar(['bar_id' => 1, 'is_active' => 0]), $this->bar(['bar_id' => 2, 'is_active' => 0])];
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->exactly(2))->method('save');

        (new MassStatus($this->buildContext(['status' => '1']), $this->filter($bars), $this->factory(), $resource))
            ->execute();

        $this->assertSame(1, $bars[0]->getData('is_active'));
        $this->assertSame(1, $bars[1]->getData('is_active'));
        $this->assertSame(['2 notification bars have been enabled.'], $this->messages['success']);
    }

    public function testMassDisableSingleBar(): void
    {
        $bar = $this->bar(['bar_id' => 1, 'is_active' => 1]);
        $resource = $this->createStub(BarResource::class);

        (new MassStatus($this->buildContext(['status' => '0']), $this->filter([$bar]), $this->factory(), $resource))
            ->execute();

        $this->assertSame(0, $bar->getData('is_active'));
        $this->assertSame(['1 notification bar has been disabled.'], $this->messages['success']);
    }

    public function testMassStatusCountsFailures(): void
    {
        $resource = $this->createStub(BarResource::class);
        $resource->method('save')->willThrowException(new \RuntimeException('x'));
        $bars = [$this->bar(['bar_id' => 1]), $this->bar(['bar_id' => 2])];

        (new MassStatus($this->buildContext(['status' => 1]), $this->filter($bars), $this->factory(), $resource))
            ->execute();

        $this->assertSame([], $this->messages['success']);
        $this->assertSame(['2 notification bars could not be updated.'], $this->messages['error']);
    }

    public function testMassStatusSingleFailureAndFilterError(): void
    {
        $resource = $this->createStub(BarResource::class);
        $resource->method('save')->willThrowException(new \RuntimeException('x'));

        (new MassStatus($this->buildContext(['status' => 1]), $this->filter([$this->bar()]), $this->factory(), $resource))
            ->execute();
        $this->assertSame(['1 notification bar could not be updated.'], $this->messages['error']);

        (new MassStatus($this->buildContext(['status' => 1]), $this->failingFilter(), $this->factory(), $resource))
            ->execute();
        $this->assertSame(['Nothing selected'], $this->messages['error']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }
}
