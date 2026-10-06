<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\Processor;
use Magento\Framework\View\Element\UiComponentFactory;
use Panth\NotificationBar\Ui\Component\Listing\Column\BarActions;
use PHPUnit\Framework\TestCase;

class BarActionsTest extends TestCase
{
    private function column(): BarActions
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('getProcessor')->willReturn($this->createStub(Processor::class));
        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturnCallback(
            static fn($route, $params = []) => '/admin/' . $route . '/id/' . $params['bar_id']
        );

        return new BarActions(
            $context,
            $this->createStub(UiComponentFactory::class),
            $url,
            [],
            ['name' => 'actions']
        );
    }

    public function testAddsEditAndDeleteActionsWithEscapedConfirmation(): void
    {
        $result = $this->column()->prepareDataSource([
            'data' => ['items' => [['bar_id' => 4, 'name' => 'Promo <b>"X"</b>']]],
        ]);

        $actions = $result['data']['items'][0]['actions'];
        $this->assertSame('/admin/panth_notificationbar/bar/edit/id/4', $actions['edit']['href']);
        $this->assertSame('Edit', (string)$actions['edit']['label']);
        $this->assertSame('/admin/panth_notificationbar/bar/delete/id/4', $actions['delete']['href']);
        $this->assertTrue($actions['delete']['post']);
        $this->assertSame(
            'Are you sure you want to delete the notification bar "Promo &lt;b&gt;&quot;X&quot;&lt;/b&gt;"?',
            (string)$actions['delete']['confirm']['message']
        );
    }

    public function testRowsWithoutIdAreLeftUntouched(): void
    {
        $source = ['data' => ['items' => [['name' => 'No id'], ['bar_id' => 1]]]];

        $result = $this->column()->prepareDataSource($source);

        $this->assertSame(['name' => 'No id'], $result['data']['items'][0]);
        $this->assertArrayHasKey('actions', $result['data']['items'][1]);
        $this->assertStringContainsString('""', (string)$result['data']['items'][1]['actions']['delete']['confirm']['message']);
    }

    public function testDataSourceWithoutItemsIsReturnedAsIs(): void
    {
        $this->assertSame(['data' => ['totalRecords' => 0]], $this->column()->prepareDataSource(['data' => ['totalRecords' => 0]]));
    }
}
