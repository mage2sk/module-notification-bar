<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\NotificationBar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Panth\NotificationBar\Model\ResourceModel\Bar\CollectionFactory;

class NotificationBar implements ArgumentInterface
{
    private const XML_PATH_ENABLED = 'panth_notification_bar/general/enabled';
    private const XML_PATH_MAX_BARS = 'panth_notification_bar/general/max_visible_bars';
    private const XML_PATH_ZINDEX = 'panth_notification_bar/general/z_index';

    private ?array $activeBars = null;

    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly RequestInterface $request,
        private readonly CustomerSession $customerSession
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getActiveBars(): array
    {
        if ($this->activeBars !== null) {
            return $this->activeBars;
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->setOrder('sort_order', 'ASC');

        $now = date('Y-m-d H:i:s');
        $collection->addFieldToFilter(
            'date_from',
            [
                ['null' => true],
                ['lteq' => $now]
            ]
        );
        $collection->addFieldToFilter(
            'date_to',
            [
                ['null' => true],
                ['gteq' => $now]
            ]
        );

        $bars = [];
        $storeId = (int) $this->storeManager->getStore()->getId();
        $isMobile = $this->isMobileDevice();
        $customerGroupId = (int) $this->customerSession->getCustomerGroupId();
        $maxBars = $this->getMaxVisibleBars();

        foreach ($collection as $bar) {
            $barData = $bar->getData();

            // Store filter
            $storeIds = $this->parseCommaSeparated($barData['store_ids'] ?? '');
            if (!empty($storeIds) && !in_array(0, $storeIds) && !in_array($storeId, $storeIds)) {
                continue;
            }

            // Device filter
            if ($isMobile && empty($barData['show_on_mobile'])) {
                continue;
            }
            if (!$isMobile && empty($barData['show_on_desktop'])) {
                continue;
            }

            // Customer group filter
            $groups = $this->parseCommaSeparated($barData['customer_groups'] ?? '');
            if (!empty($groups) && !in_array($customerGroupId, $groups)) {
                continue;
            }

            // Page targeting filter
            if (!$this->matchesCurrentPage($barData)) {
                continue;
            }

            // Use mobile content if available and on mobile
            if ($isMobile && !empty($barData['mobile_content'])) {
                $barData['content'] = $barData['mobile_content'];
            }

            $bars[] = $barData;

            if (count($bars) >= $maxBars) {
                break;
            }
        }

        $this->activeBars = $bars;
        return $this->activeBars;
    }

    public function getBarHtml(array $bar): string
    {
        $barId = (int) ($bar['bar_id'] ?? 0);
        $position = $bar['position'] ?? 'top_fixed';
        $animation = $bar['animation'] ?? 'slide_down';
        $dismissible = !empty($bar['is_dismissible']) ? '1' : '0';
        $cookieDuration = (int) ($bar['cookie_duration'] ?? 7);
        $autoClose = (int) ($bar['auto_close_seconds'] ?? 0);
        $textColor = $bar['text_color'] ?? '#FFFFFF';
        $fontSize = (int) ($bar['font_size'] ?? 14);
        $padding = $bar['bar_padding'] ?? '10px 20px';
        $zIndex = $this->getZIndex();

        // Background
        $bgStyle = $this->getBackgroundStyle($bar);

        // Height
        $heightStyle = '';
        $barHeight = (int) ($bar['bar_height'] ?? 0);
        if ($barHeight > 0) {
            $heightStyle = 'min-height:' . $barHeight . 'px;';
        }

        $html = '<div class="panth-nbar panth-nbar-pos-' . $this->escAttr($position) . '"'
            . ' data-bar-id="' . $barId . '"'
            . ' data-position="' . $this->escAttr($position) . '"'
            . ' data-animation="' . $this->escAttr($animation) . '"'
            . ' data-dismissible="' . $dismissible . '"'
            . ' data-cookie-duration="' . $cookieDuration . '"'
            . ' data-auto-close="' . $autoClose . '"'
            . ' style="' . $bgStyle . 'color:' . $this->escAttr($textColor) . ';'
            . 'font-size:' . $fontSize . 'px;'
            . 'padding:' . $this->escAttr($padding) . ';'
            . $heightStyle
            . 'z-index:' . $zIndex . ';">';

        $html .= '<div class="panth-nbar-content">';

        // Icon
        $icon = $bar['icon'] ?? '';
        if ($icon !== '' && $icon !== null) {
            $svg = $this->getBuiltInIcon($icon);
            if ($svg !== '') {
                $html .= '<span class="panth-nbar-icon">' . $svg . '</span>';
            }
        }

        // Text content
        $content = $bar['content'] ?? '';

        // Countdown replacement
        if (!empty($bar['countdown_enabled']) && !empty($bar['countdown_end_date'])) {
            $label = $bar['countdown_label'] ?? '';
            $expiredText = $bar['countdown_expired_text'] ?? 'Expired';
            $countdownHtml = '';
            if ($label !== '') {
                $countdownHtml .= '<span class="panth-nbar-cd-label">'
                    . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ' </span>';
            }
            $countdownHtml .= '<span class="panth-nbar-countdown"'
                . ' data-end-date="' . $this->escAttr($bar['countdown_end_date']) . '"'
                . ' data-expired-text="' . $this->escAttr($expiredText) . '">'
                . '</span>';
            $content = str_replace('{countdown}', $countdownHtml, $content);
        }

        $html .= '<span class="panth-nbar-text">' . $content . '</span>';

        // CTA button
        if (!empty($bar['cta_enabled']) && !empty($bar['cta_text'])) {
            $ctaUrl = $bar['cta_url'] ?? '#';
            $ctaBg = $bar['cta_bg_color'] ?? '#FFFFFF';
            $ctaColor = $bar['cta_text_color'] ?? '#1F2937';
            $target = !empty($bar['cta_open_new_tab']) ? ' target="_blank" rel="noopener"' : '';
            $html .= ' <a class="panth-nbar-cta" href="'
                . $this->escAttr($ctaUrl) . '"'
                . $target
                . ' style="background:' . $this->escAttr($ctaBg) . ';color:'
                . $this->escAttr($ctaColor) . ';">'
                . htmlspecialchars($bar['cta_text'], ENT_QUOTES, 'UTF-8')
                . '</a>';
        }

        $html .= '</div>';

        // Close button
        if (!empty($bar['is_dismissible'])) {
            $html .= '<button type="button" class="panth-nbar-close" aria-label="Close">'
                . '<svg width="14" height="14" viewBox="0 0 14 14" fill="none">'
                . '<path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>'
                . '</svg></button>';
        }

        $html .= '</div>';

        return $html;
    }

    public function getBarCss(array $bar): string
    {
        $barId = (int) ($bar['bar_id'] ?? 0);
        $css = '';

        // Custom CSS
        $customCss = $bar['custom_css'] ?? '';
        if ($customCss !== '') {
            $css .= '.panth-nbar[data-bar-id="' . $barId . '"] { ' . $customCss . ' }' . "\n";
        }

        return $css;
    }

    public function matchesCurrentPage(array $bar): bool
    {
        $targeting = $bar['page_targeting'] ?? 'all';

        if ($targeting === 'all') {
            return true;
        }

        $currentUrl = $this->request->getRequestUri();
        $currentPath = trim(parse_url($currentUrl, PHP_URL_PATH) ?? '/', '/');
        $moduleName = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName = $this->request->getActionName();
        $fullAction = $moduleName . '_' . $controllerName . '_' . $actionName;

        $matches = false;

        // Check target page types
        $pageTypes = $this->parseCommaSeparatedString($bar['target_page_types'] ?? '');
        if (!empty($pageTypes)) {
            foreach ($pageTypes as $pageType) {
                $pageType = trim($pageType);
                if ($pageType === 'homepage' && ($currentPath === '' || $currentPath === '/')) {
                    $matches = true;
                    break;
                }
                if ($pageType === 'category' && $fullAction === 'catalog_category_view') {
                    $matches = true;
                    break;
                }
                if ($pageType === 'product' && $fullAction === 'catalog_product_view') {
                    $matches = true;
                    break;
                }
                if ($pageType === 'cart' && $fullAction === 'checkout_cart_index') {
                    $matches = true;
                    break;
                }
                if ($pageType === 'checkout' && str_starts_with($fullAction, 'checkout_index')) {
                    $matches = true;
                    break;
                }
                if ($pageType === 'cms' && $moduleName === 'cms') {
                    $matches = true;
                    break;
                }
                if ($pageType === 'search' && $fullAction === 'catalogsearch_result_index') {
                    $matches = true;
                    break;
                }
                if ($pageType === 'account' && $moduleName === 'customer') {
                    $matches = true;
                    break;
                }
            }
        }

        // Check target URLs (pattern matching)
        if (!$matches) {
            $targetUrls = $this->parseCommaSeparatedString($bar['target_urls'] ?? '');
            if (!empty($targetUrls)) {
                foreach ($targetUrls as $pattern) {
                    $pattern = trim($pattern, ' /');
                    if ($pattern === '') {
                        continue;
                    }
                    // Convert * wildcard to regex
                    $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#i';
                    if (preg_match($regex, $currentPath)) {
                        $matches = true;
                        break;
                    }
                }
            }
        }

        // Check URL params
        if (!$matches && !empty($bar['target_url_params'])) {
            $params = $this->parseCommaSeparatedString($bar['target_url_params']);
            $currentParams = $this->request->getParams();
            $paramMatch = true;
            foreach ($params as $param) {
                if (str_contains($param, '=')) {
                    [$key, $value] = explode('=', $param, 2);
                    $key = trim($key);
                    $value = trim($value);
                    if (!isset($currentParams[$key]) || $currentParams[$key] !== $value) {
                        $paramMatch = false;
                        break;
                    }
                }
            }
            if ($paramMatch && !empty($params)) {
                $matches = true;
            }
        }

        // If no page types and no target urls specified, treat as match for 'specific'
        $pageTypes = $this->parseCommaSeparatedString($bar['target_page_types'] ?? '');
        $targetUrls = $this->parseCommaSeparatedString($bar['target_urls'] ?? '');
        $targetParams = $this->parseCommaSeparatedString($bar['target_url_params'] ?? '');
        if (empty($pageTypes) && empty($targetUrls) && empty($targetParams)) {
            $matches = true;
        }

        return $targeting === 'exclude' ? !$matches : $matches;
    }

    public function getBuiltInIcon(string $name): string
    {
        $icons = [
            'info' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
            'warning' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
            'success' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            'promo' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"/></svg>',
            'star' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>',
            'bell' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>',
            'gift' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm2 0a1 1 0 10-1-1v1h1zm-6 6v5a2 2 0 002 2h6a2 2 0 002-2v-5H5z" clip-rule="evenodd"/></svg>',
            'truck' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M0 4a1 1 0 011-1h11a1 1 0 011 1v11h-1.5a2.5 2.5 0 00-5 0H7.5a2.5 2.5 0 00-5 0H1a1 1 0 01-1-1V4zm13 0v4h3.5l-2-4H13z"/><path d="M13 8V4h.38l2.5 5H18a1 1 0 011 1v2h-1.05a2.5 2.5 0 00-4.9 0H13V8z"/></svg>',
            'percent' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zM7.5 8a.5.5 0 100-1 .5.5 0 000 1zm5 4a.5.5 0 100-1 .5.5 0 000 1zm-5.354.854a.5.5 0 00.708 0l5-5a.5.5 0 00-.708-.708l-5 5a.5.5 0 000 .708z" clip-rule="evenodd"/></svg>',
            'clock' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>',
        ];

        return $icons[$name] ?? '';
    }

    public function getMaxVisibleBars(): int
    {
        $max = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_BARS,
            ScopeInterface::SCOPE_STORE
        );
        return $max > 0 ? $max : 5;
    }

    public function getZIndex(): int
    {
        $zIndex = (int) $this->scopeConfig->getValue(
            self::XML_PATH_ZINDEX,
            ScopeInterface::SCOPE_STORE
        );
        return $zIndex > 0 ? $zIndex : 9999;
    }

    private function getBackgroundStyle(array $bar): string
    {
        $type = $bar['background_type'] ?? 'color';

        if ($type === 'gradient' && !empty($bar['background_gradient'])) {
            return 'background:' . $bar['background_gradient'] . ';';
        }

        if ($type === 'image' && !empty($bar['background_image'])) {
            $color = $bar['background_color'] ?? '#1F2937';
            return 'background:' . $this->escAttr($color)
                . ' url(' . $this->escAttr($bar['background_image'])
                . ') center/cover no-repeat;';
        }

        $color = $bar['background_color'] ?? '#1F2937';
        return 'background-color:' . $this->escAttr($color) . ';';
    }

    private function isMobileDevice(): bool
    {
        $userAgent = $this->request->getServer('HTTP_USER_AGENT', '');
        if ($userAgent === '' || $userAgent === null) {
            return false;
        }
        return (bool) preg_match(
            '/Mobile|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i',
            (string) $userAgent
        );
    }

    /**
     * @return int[]
     */
    private function parseCommaSeparated(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }
        return array_map('intval', array_filter(explode(',', $value), function ($v) {
            return trim($v) !== '';
        }));
    }

    /**
     * @return string[]
     */
    private function parseCommaSeparatedString(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $value)), function ($v) {
            return $v !== '';
        });
    }

    private function escAttr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
