<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Controller\Adminhtml\Bar;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use Panth\NotificationBar\Controller\Adminhtml\Bar\Edit;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class EditTest extends ControllerTestCase
{
    /**
     * @var array
     */
    private array $titles = [];

    /**
     * @var string|null
     */
    private ?string $activeMenu = null;

    private function pageFactory(): PageFactory
    {
        $this->titles = [];
        $this->activeMenu = null;

        $title = $this->createStub(Title::class);
        $title->method('prepend')->willReturnCallback(function ($text) {
            $this->titles[] = (string)$text;
        });
        $config = $this->createStub(Config::class);
        $config->method('getTitle')->willReturn($title);
        $page = $this->createStub(Page::class);
        $page->method('getConfig')->willReturn($config);
        $page->method('setActiveMenu')->willReturnCallback(function ($menu) use (&$page) {
            $this->activeMenu = $menu;
            return $page;
        });
        $factory = $this->createStub(PageFactory::class);
        $factory->method('create')->willReturn($page);

        return $factory;
    }

    public function testNewBarShowsNewTitle(): void
    {
        $resource = $this->createMock(BarResource::class);
        $resource->expects($this->never())->method('load');

        $result = (new Edit($this->buildContext(), $this->pageFactory(), $this->barFactory(), $resource))->execute();

        $this->assertInstanceOf(Page::class, $result);
        $this->assertSame(['New Bar'], $this->titles);
        $this->assertSame('Panth_NotificationBar::manage_bars', $this->activeMenu);
    }

    public function testExistingBarShowsItsName(): void
    {
        $bar = $this->bar();
        $resource = $this->createStub(BarResource::class);
        $resource->method('load')->willReturnCallback(function ($model, $id) use ($resource) {
            $model->setData(['bar_id' => $id, 'name' => 'Summer Sale']);
            return $resource;
        });

        (new Edit($this->buildContext(['bar_id' => '6']), $this->pageFactory(), $this->barFactory($bar), $resource))
            ->execute();

        $this->assertSame(['Edit: Summer Sale'], $this->titles);
    }

    public function testMissingBarRedirectsToGridWithError(): void
    {
        $resource = $this->createStub(BarResource::class);

        (new Edit($this->buildContext(['bar_id' => 99]), $this->pageFactory(), $this->barFactory(), $resource))
            ->execute();

        $this->assertSame(['This notification bar no longer exists.'], $this->messages['error']);
        $this->assertSame('*/*/', $this->redirect['path']);
        $this->assertSame([], $this->titles);
    }
}
