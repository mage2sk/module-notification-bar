<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Controller\Adminhtml\Bar;

use Magento\Framework\Exception\LocalizedException;
use Panth\NotificationBar\Controller\Adminhtml\Bar\Delete;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class DeleteTest extends ControllerTestCase
{
    public function testMissingIdShowsErrorWithoutTouchingTheResource(): void
    {
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->never())->method('delete');

        (new Delete($this->buildContext(['bar_id' => 'abc']), $this->barFactory(), $resource))->execute();

        $this->assertSame(['We cannot find a notification bar to delete.'], $this->messages['error']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }

    public function testDeletesLoadedBar(): void
    {
        $bar = $this->bar();
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->once())->method('load')->with($bar, 4);
        $resource->expects($this->once())->method('delete')->with($bar);

        (new Delete($this->buildContext(['bar_id' => '4']), $this->barFactory($bar), $resource))->execute();

        $this->assertSame(['The notification bar has been deleted.'], $this->messages['success']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }

    public function testLocalizedExceptionMessageIsShown(): void
    {
        $resource = $this->createStub(BarResource::class);
        $resource->method('delete')->willThrowException(new LocalizedException(__('Locked bar')));

        (new Delete($this->buildContext(['bar_id' => 4]), $this->barFactory(), $resource))->execute();

        $this->assertSame(['Locked bar'], $this->messages['error']);
        $this->assertSame([], $this->messages['success']);
    }

    public function testGenericExceptionIsReportedWithFriendlyMessage(): void
    {
        $resource = $this->createStub(BarResource::class);
        $resource->method('delete')->willThrowException(new \RuntimeException('db down'));

        (new Delete($this->buildContext(['bar_id' => 4]), $this->barFactory(), $resource))->execute();

        $this->assertSame(['Something went wrong while deleting the notification bar.'], $this->messages['exception']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }
}
