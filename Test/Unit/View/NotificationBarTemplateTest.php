<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\View;

use PHPUnit\Framework\TestCase;

class NotificationBarTemplateTest extends TestCase
{
    private string $template = '';

    protected function setUp(): void
    {
        $path = dirname(__DIR__, 3) . '/view/frontend/templates/notification-bar.phtml';
        $this->assertTrue(is_file($path));
        $this->template = (string) file_get_contents($path);
    }

    private function phoneBlock(): string
    {
        $start = strpos($this->template, '@media (max-width: 768px)');
        $this->assertNotFalse($start);
        $end = strpos($this->template, '@media (prefers-reduced-motion: reduce)', $start);
        $this->assertNotFalse($end);
        return substr($this->template, $start, $end - $start);
    }

    public function testPhoneBarIsAtMostFortyPixelsTall(): void
    {
        $this->assertMatchesRegularExpression(
            '/\.panth-nbar \{\s+padding: 8px 48px 8px 12px !important;\s+min-height: 40px;\s+\}/',
            $this->phoneBlock()
        );
        $this->assertStringNotContainsString('min-height: 48px', $this->template);
    }

    public function testPhoneCloseButtonFillsBarHeightWithWideTapArea(): void
    {
        $this->assertMatchesRegularExpression(
            '/\.panth-nbar-close \{\s+right: 2px;\s+top: 0;\s+bottom: 0;\s+transform: none;'
            . '\s+min-width: 44px;\s+min-height: 40px;\s+height: 100%;\s+\}/',
            $this->phoneBlock()
        );
    }

    public function testPhoneCallToActionKeepsFortyFourPixelTapTarget(): void
    {
        $this->assertMatchesRegularExpression(
            '/\.panth-nbar-cta \{\s+display: inline-flex;\s+align-items: center;\s+min-height: 44px;/',
            $this->phoneBlock()
        );
    }

    public function testBottomBarsStayHiddenOnCheckout(): void
    {
        $this->assertStringContainsString(
            'body.checkout-index-index .panth-nbar-pos-bottom_fixed,',
            $this->template
        );
        $this->assertStringContainsString(
            'body.checkout-index-index .panth-nbar-pos-bottom_floating,',
            $this->template
        );
    }
}
