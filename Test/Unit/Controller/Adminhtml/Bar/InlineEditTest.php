<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Controller\Adminhtml\Bar;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Panth\NotificationBar\Controller\Adminhtml\Bar\InlineEdit;
use Panth\NotificationBar\Model\BarFactory;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class InlineEditTest extends ControllerTestCase
{
    /**
     * @var array|null
     */
    private ?array $json = null;

    private function jsonFactory(): JsonFactory
    {
        $this->json = null;
        $json = $this->createStub(Json::class);
        $json->method('setData')->willReturnCallback(function ($data) use (&$json) {
            $this->json = $data;
            return $json;
        });
        $factory = $this->createStub(JsonFactory::class);
        $factory->method('create')->willReturn($json);

        return $factory;
    }

    private function messages(): array
    {
        return array_map('strval', $this->json['messages']);
    }

    private function loadingResource(array $existing): BarResource
    {
        $resource = $this->createStub(BarResource::class);
        $resource->method('load')->willReturnCallback(function ($model, $id) use ($existing, $resource) {
            if (isset($existing[$id])) {
                $model->setData($existing[$id]);
            }
            return $resource;
        });

        return $resource;
    }

    private function freshBars(): BarFactory
    {
        $factory = $this->createStub(BarFactory::class);
        $factory->method('create')->willReturnCallback(fn() => $this->bar());

        return $factory;
    }

    public function testRejectsNonAjaxRequests(): void
    {
        $context = $this->buildContext(['items' => [1 => ['name' => 'x']]]);
        (new InlineEdit($context, $this->freshBars(), $this->createStub(BarResource::class), $this->jsonFactory()))
            ->execute();

        $this->assertTrue($this->json['error']);
        $this->assertSame(['Please correct the data sent.'], $this->messages());
    }

    public function testRejectsEmptyItems(): void
    {
        $context = $this->buildContext(['isAjax' => true, 'items' => []]);
        (new InlineEdit($context, $this->freshBars(), $this->createStub(BarResource::class), $this->jsonFactory()))
            ->execute();

        $this->assertTrue($this->json['error']);
    }

    public function testMergesRowDataAndIgnoresPostedPrimaryKey(): void
    {
        $saved = [];
        $resource = $this->loadingResource([3 => ['bar_id' => 3, 'name' => 'Old', 'sort_order' => 1]]);
        $resource->method('save')->willReturnCallback(function ($model) use (&$saved, $resource) {
            $saved[] = $model->getData();
            return $resource;
        });

        $context = $this->buildContext([
            'isAjax' => true,
            'items' => [3 => ['bar_id' => 77, 'name' => 'New']],
        ]);
        (new InlineEdit($context, $this->freshBars(), $resource, $this->jsonFactory()))->execute();

        $this->assertSame([['bar_id' => 3, 'name' => 'New', 'sort_order' => 1]], $saved);
        $this->assertFalse($this->json['error']);
        $this->assertSame([], $this->json['messages']);
    }

    public function testReportsMissingBarsAndInvalidRowsButSavesTheRest(): void
    {
        $savedIds = [];
        $resource = $this->loadingResource([
            1 => ['bar_id' => 1, 'name' => 'One'],
            2 => ['bar_id' => 2, 'name' => 'Two'],
        ]);
        $resource->method('save')->willReturnCallback(function ($model) use (&$savedIds, $resource) {
            $savedIds[] = $model->getId();
            return $resource;
        });

        $context = $this->buildContext([
            'isAjax' => true,
            'items' => [1 => ['name' => 'A'], 2 => 'not-an-array', 9 => ['name' => 'Ghost']],
        ]);
        (new InlineEdit($context, $this->freshBars(), $resource, $this->jsonFactory()))->execute();

        $this->assertSame([1], $savedIds);
        $this->assertTrue($this->json['error']);
        $this->assertSame([
            '[Bar ID: 2] This notification bar no longer exists.',
            '[Bar ID: 9] This notification bar no longer exists.',
        ], $this->messages());
    }

    public function testSaveExceptionsAreCollectedPerRow(): void
    {
        $resource = $this->loadingResource([1 => ['bar_id' => 1], 2 => ['bar_id' => 2]]);
        $resource->method('save')->willReturnCallback(function ($model) {
            if ((int)$model->getId() === 1) {
                throw new LocalizedException(__('Invalid color'));
            }
            throw new \RuntimeException('db');
        });

        $context = $this->buildContext(['isAjax' => true, 'items' => [1 => ['x' => 1], 2 => ['x' => 2]]]);
        (new InlineEdit($context, $this->freshBars(), $resource, $this->jsonFactory()))->execute();

        $this->assertTrue($this->json['error']);
        $this->assertSame([
            '[Bar ID: 1] Invalid color',
            '[Bar ID: 2] Something went wrong while saving.',
        ], $this->messages());
    }
}
