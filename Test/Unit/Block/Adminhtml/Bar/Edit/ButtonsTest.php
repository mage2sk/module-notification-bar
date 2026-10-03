<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Block\Adminhtml\Bar\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Panth\NotificationBar\Block\Adminhtml\Bar\Edit\DeleteButton;
use Panth\NotificationBar\Block\Adminhtml\Bar\Edit\GenericButton;
use Panth\NotificationBar\Block\Adminhtml\Bar\Edit\SaveAndContinueButton;
use Panth\NotificationBar\Block\Adminhtml\Bar\Edit\SaveButton;
use PHPUnit\Framework\TestCase;

class ButtonsTest extends TestCase
{
    private function context($barId): Context
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(
            static fn($key) => $key === 'bar_id' ? $barId : null
        );
        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturnCallback(
            static fn($route, $params = []) => 'https://admin/' . $route . '?' . http_build_query($params)
        );
        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);
        $context->method('getUrlBuilder')->willReturn($url);

        return $context;
    }

    public function testGenericButtonReadsBarIdFromRequest(): void
    {
        $this->assertSame(9, (new GenericButton($this->context('9')))->getBarId());
        $this->assertNull((new GenericButton($this->context(null)))->getBarId());
        $this->assertNull((new GenericButton($this->context('0')))->getBarId());
    }

    public function testGenericButtonBuildsUrls(): void
    {
        $this->assertSame(
            'https://admin/*/*/edit?bar_id=2',
            (new GenericButton($this->context(null)))->getUrl('*/*/edit', ['bar_id' => 2])
        );
    }

    public function testDeleteButtonHiddenForNewBar(): void
    {
        $this->assertSame([], (new DeleteButton($this->context(null)))->getButtonData());
    }

    public function testDeleteButtonConfirmsAndPostsToDeleteUrl(): void
    {
        $data = (new DeleteButton($this->context('5')))->getButtonData();

        $this->assertSame('delete', $data['class']);
        $this->assertSame(20, $data['sort_order']);
        $this->assertStringStartsWith('deleteConfirm(\'Are you sure', $data['on_click']);
        $this->assertStringContainsString('\'https://admin/*/*/delete?bar_id=5\'', $data['on_click']);
    }

    public function testSaveButtons(): void
    {
        $save = (new SaveButton($this->context(null)))->getButtonData();
        $continue = (new SaveAndContinueButton($this->context(null)))->getButtonData();

        $this->assertSame('save primary', $save['class']);
        $this->assertSame('save', $save['data_attribute']['mage-init']['button']['event']);
        $this->assertSame('save', $save['data_attribute']['form-role']);
        $this->assertSame('saveAndContinueEdit', $continue['data_attribute']['mage-init']['button']['event']);
        $this->assertGreaterThan($continue['sort_order'], $save['sort_order']);
    }
}
