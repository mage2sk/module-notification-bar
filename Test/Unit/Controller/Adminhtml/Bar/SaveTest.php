<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Controller\Adminhtml\Bar;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Panth\NotificationBar\Controller\Adminhtml\Bar\Save;
use Panth\NotificationBar\Model\Bar;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class SaveTest extends ControllerTestCase
{
    /**
     * @var array|null
     */
    private ?array $savedData = null;

    private function savingResource(array $existing = [], ?\Throwable $error = null, int $newId = 50): BarResource
    {
        $this->savedData = null;
        $resource = $this->createStub(BarResource::class);
        $resource->method('load')->willReturnCallback(function (Bar $model, $id) use ($existing, $resource) {
            if (isset($existing[$id])) {
                $model->setData($existing[$id]);
            }
            return $resource;
        });
        $resource->method('save')->willReturnCallback(
            function (Bar $model) use ($error, $newId, $resource) {
                if ($error) {
                    throw $error;
                }
                $this->savedData = $model->getData();
                if (!$model->getId()) {
                    $model->setId($newId);
                }
                return $resource;
            }
        );

        return $resource;
    }

    public function testEmptyPostRedirectsToGrid(): void
    {
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->never())->method('save');

        (new Save(
            $this->buildContext([], []),
            $this->barFactory(),
            $resource,
            $this->createStub(DataPersistorInterface::class)
        ))->execute();

        $this->assertSame('*/*/', $this->redirect['path']);
        $this->assertSame([], $this->messages['success']);
    }

    public function testNewBarIsSavedWithMultiSelectsImplodedAndPersistorCleared(): void
    {
        $persistor = $this->createMock(DataPersistorInterface::class);
        $persistor->expects($this->once())->method('clear')->with('panth_notification_bar');
        $persistor->expects($this->never())->method('set');

        $post = [
            'bar_id' => '',
            'name' => 'Promo',
            'store_ids' => ['1', '2'],
            'customer_groups' => ['0', '1'],
            'target_page_types' => ['home', 'cart'],
            'target_countries' => ['US'],
            'target_urls' => 'sale/*',
        ];
        (new Save($this->buildContext([], $post), $this->barFactory(), $this->savingResource(), $persistor))
            ->execute();

        $this->assertArrayNotHasKey('bar_id', $this->savedData);
        $this->assertSame('1,2', $this->savedData['store_ids']);
        $this->assertSame('0,1', $this->savedData['customer_groups']);
        $this->assertSame('home,cart', $this->savedData['target_page_types']);
        $this->assertSame('US', $this->savedData['target_countries']);
        $this->assertSame('sale/*', $this->savedData['target_urls']);
        $this->assertSame(['The notification bar has been saved.'], $this->messages['success']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }

    public function testSaveAndContinueRedirectsToEditWithNewId(): void
    {
        (new Save(
            $this->buildContext(['back' => 'edit'], ['name' => 'X']),
            $this->barFactory(),
            $this->savingResource([], null, 51),
            $this->createStub(DataPersistorInterface::class)
        ))->execute();

        $this->assertSame(['path' => '*/*/edit', 'params' => ['bar_id' => 51]], $this->redirect);
    }

    public function testExistingBarIsMergedWithPostedData(): void
    {
        $resource = $this->savingResource([7 => ['bar_id' => 7, 'name' => 'Old', 'content' => 'Keep me']]);

        (new Save(
            $this->buildContext([], ['bar_id' => '7', 'name' => 'New']),
            $this->barFactory(),
            $resource,
            $this->createStub(DataPersistorInterface::class)
        ))->execute();

        $this->assertSame(7, (int)$this->savedData['bar_id']);
        $this->assertSame('New', $this->savedData['name']);
        $this->assertSame('Keep me', $this->savedData['content']);
    }

    public function testUnknownBarIdShowsErrorAndDoesNotSave(): void
    {
        $persistor = $this->createMock(DataPersistorInterface::class);
        $persistor->expects($this->never())->method('set');

        (new Save(
            $this->buildContext([], ['bar_id' => '404', 'name' => 'x']),
            $this->barFactory(),
            $this->savingResource(),
            $persistor
        ))->execute();

        $this->assertNull($this->savedData);
        $this->assertSame(['This notification bar no longer exists.'], $this->messages['error']);
        $this->assertSame('*/*/', $this->redirect['path']);
    }

    public function testLocalizedErrorOnNewBarPersistsFormDataAndReturnsToNewForm(): void
    {
        $persistor = $this->createMock(DataPersistorInterface::class);
        $persistor->expects($this->once())->method('set')->with(
            'panth_notification_bar',
            ['name' => 'Bad', 'store_ids' => '1,3']
        );

        (new Save(
            $this->buildContext([], ['name' => 'Bad', 'store_ids' => ['1', '3']]),
            $this->barFactory(),
            $this->savingResource([], new LocalizedException(__('Name taken'))),
            $persistor
        ))->execute();

        $this->assertSame(['Name taken'], $this->messages['error']);
        $this->assertSame('*/*/new', $this->redirect['path']);
    }

    public function testGenericErrorOnExistingBarReturnsToEditForm(): void
    {
        (new Save(
            $this->buildContext([], ['bar_id' => 7, 'name' => 'Y']),
            $this->barFactory(),
            $this->savingResource([7 => ['bar_id' => 7]], new \RuntimeException('db')),
            $this->createStub(DataPersistorInterface::class)
        ))->execute();

        $this->assertSame(['Something went wrong while saving the notification bar.'], $this->messages['exception']);
        $this->assertSame(['path' => '*/*/edit', 'params' => ['bar_id' => 7]], $this->redirect);
    }
}
