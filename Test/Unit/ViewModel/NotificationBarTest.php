<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\ViewModel;

use Magento\Cms\Model\Template\Filter;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Panth\NotificationBar\Model\ResourceModel\Bar\Collection;
use Panth\NotificationBar\Model\ResourceModel\Bar\CollectionFactory;
use Panth\NotificationBar\ViewModel\NotificationBar;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NotificationBarTest extends TestCase
{
    /**
     * @var array
     */
    private array $config = [];

    /**
     * @var array
     */
    private array $flags = [];

    /**
     * @var array
     */
    private array $request = [];

    /**
     * @var array
     */
    private array $collectionFilters = [];

    /**
     * @var array
     */
    private array $collectionOrder = [];

    /**
     * @var bool
     */
    private bool $filterThrows = false;

    /**
     * @var int
     */
    private int $collectionCreated = 0;

    protected function setUp(): void
    {
        $this->config = [];
        $this->flags = [];
        $this->request = [
            'uri' => '/',
            'module' => 'cms',
            'controller' => 'index',
            'action' => 'index',
            'params' => [],
        ];
        $this->collectionFilters = [];
        $this->collectionOrder = [];
        $this->filterThrows = false;
        $this->collectionCreated = 0;
    }

    private function viewModel(array $rows = [], int $storeId = 1, int $groupId = 0): NotificationBar
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(fn($path) => $this->config[$path] ?? null);
        $scopeConfig->method('isSetFlag')->willReturnCallback(fn($path) => (bool)($this->flags[$path] ?? false));

        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $request = $this->createStub(Http::class);
        $request->method('getRequestUri')->willReturnCallback(fn() => $this->request['uri']);
        $request->method('getModuleName')->willReturnCallback(fn() => $this->request['module']);
        $request->method('getControllerName')->willReturnCallback(fn() => $this->request['controller']);
        $request->method('getActionName')->willReturnCallback(fn() => $this->request['action']);
        $request->method('getParams')->willReturnCallback(fn() => $this->request['params']);

        $session = $this->createStub(CustomerSession::class);
        $session->method('getCustomerGroupId')->willReturn($groupId);

        $timezone = $this->createStub(TimezoneInterface::class);
        $timezone->method('getConfigTimezone')->willReturn('UTC');

        $filter = $this->createStub(Filter::class);
        $filter->method('setStoreId')->willReturnSelf();
        $filter->method('filter')->willReturnCallback(function ($content) {
            if ($this->filterThrows) {
                throw new \RuntimeException('boom');
            }
            return '[' . $content . ']';
        });
        $filterProvider = $this->createStub(FilterProvider::class);
        $filterProvider->method('getBlockFilter')->willReturn($filter);

        $items = array_map(static fn(array $row) => new DataObject($row), $rows);
        $collection = $this->createStub(Collection::class);
        $collection->method('addFieldToFilter')->willReturnCallback(
            function ($field, $cond) use (&$collection) {
                $this->collectionFilters[] = [$field, $cond];
                return $collection;
            }
        );
        $collection->method('setOrder')->willReturnCallback(
            function ($field, $dir) use (&$collection) {
                $this->collectionOrder = [$field, $dir];
                return $collection;
            }
        );
        $collection->method('getIterator')->willReturnCallback(static fn() => new \ArrayIterator($items));
        $factory = $this->createStub(CollectionFactory::class);
        $factory->method('create')->willReturnCallback(function () use ($collection) {
            $this->collectionCreated++;
            return $collection;
        });

        return new NotificationBar(
            $factory,
            $scopeConfig,
            $storeManager,
            $request,
            $session,
            $timezone,
            $filterProvider
        );
    }

    private function page(string $module, string $controller, string $action, string $uri = '/x'): void
    {
        $this->request['module'] = $module;
        $this->request['controller'] = $controller;
        $this->request['action'] = $action;
        $this->request['uri'] = $uri;
    }

    private function visibleBar(array $data = []): array
    {
        return array_merge(['bar_id' => 1, 'show_on_mobile' => 1, 'show_on_desktop' => 1], $data);
    }

    public function testIsEnabledReadsTheConfigFlag(): void
    {
        $this->assertFalse($this->viewModel()->isEnabled());
        $this->flags['panth_notification_bar/general/enabled'] = true;
        $this->assertTrue($this->viewModel()->isEnabled());
    }

    public function testMaxVisibleBarsAndZIndexFallBackToDefaults(): void
    {
        $vm = $this->viewModel();
        $this->assertSame(5, $vm->getMaxVisibleBars());
        $this->assertSame(40, $vm->getZIndex());

        $this->config['panth_notification_bar/general/max_visible_bars'] = '-2';
        $this->config['panth_notification_bar/display/z_index'] = '0';
        $this->assertSame(5, $vm->getMaxVisibleBars());
        $this->assertSame(40, $vm->getZIndex());
    }

    public function testMaxVisibleBarsAndZIndexUseConfiguredValues(): void
    {
        $this->config['panth_notification_bar/general/max_visible_bars'] = '3';
        $this->config['panth_notification_bar/display/z_index'] = '999';
        $vm = $this->viewModel();
        $this->assertSame(3, $vm->getMaxVisibleBars());
        $this->assertSame(999, $vm->getZIndex());
    }

    public function testActiveBarsQueryFiltersActiveAndNotExpiredSortedBySortOrder(): void
    {
        $this->viewModel()->getActiveBars();

        $this->assertSame(['is_active', 1], $this->collectionFilters[0]);
        $this->assertSame('date_to', $this->collectionFilters[1][0]);
        $this->assertSame(['null' => true], $this->collectionFilters[1][1][0]);
        $this->assertSame(gmdate('Y-m-d') . ' 00:00:00', $this->collectionFilters[1][1][1]['gteq']);
        $this->assertSame(['sort_order', 'ASC'], $this->collectionOrder);
    }

    public function testActiveBarsAreMemoised(): void
    {
        $vm = $this->viewModel([$this->visibleBar()]);
        $first = $vm->getActiveBars();
        $second = $vm->getActiveBars();

        $this->assertSame($first, $second);
        $this->assertCount(1, $first);
        $this->assertSame(1, $this->collectionCreated);
    }

    public function testActiveBarsFilterByStore(): void
    {
        $rows = [
            $this->visibleBar(['bar_id' => 1, 'store_ids' => '']),
            $this->visibleBar(['bar_id' => 2, 'store_ids' => '0']),
            $this->visibleBar(['bar_id' => 3, 'store_ids' => '2,3']),
            $this->visibleBar(['bar_id' => 4, 'store_ids' => '3,1']),
        ];

        $ids = array_column($this->viewModel($rows, 1)->getActiveBars(), 'bar_id');

        $this->assertSame([1, 2, 4], $ids);
    }

    public function testActiveBarsSkipBarsHiddenOnAllDevices(): void
    {
        $rows = [
            ['bar_id' => 1, 'show_on_mobile' => 0, 'show_on_desktop' => 0],
            ['bar_id' => 2, 'show_on_mobile' => 1, 'show_on_desktop' => 0],
            ['bar_id' => 3, 'show_on_mobile' => 0, 'show_on_desktop' => 1],
        ];

        $ids = array_column($this->viewModel($rows)->getActiveBars(), 'bar_id');

        $this->assertSame([2, 3], $ids);
    }

    public function testActiveBarsFilterByCustomerGroupIncludingGuestGroupZero(): void
    {
        $rows = [
            $this->visibleBar(['bar_id' => 1, 'customer_groups' => '']),
            $this->visibleBar(['bar_id' => 2, 'customer_groups' => '0,1']),
            $this->visibleBar(['bar_id' => 3, 'customer_groups' => '1,2']),
        ];

        $this->assertSame([1, 2], array_column($this->viewModel($rows, 1, 0)->getActiveBars(), 'bar_id'));
        $this->assertSame([1, 2, 3], array_column($this->viewModel($rows, 1, 1)->getActiveBars(), 'bar_id'));
    }

    public function testActiveBarsApplyPageTargeting(): void
    {
        $this->page('catalog', 'product', 'view', '/some-product.html');
        $rows = [
            $this->visibleBar(['bar_id' => 1, 'page_targeting' => 'specific', 'target_page_types' => 'category']),
            $this->visibleBar(['bar_id' => 2, 'page_targeting' => 'specific', 'target_page_types' => 'product']),
            $this->visibleBar(['bar_id' => 3, 'page_targeting' => 'exclude', 'target_page_types' => 'product']),
        ];

        $this->assertSame([2], array_column($this->viewModel($rows)->getActiveBars(), 'bar_id'));
    }

    public function testCheckoutOnlyShowsBarsThatTargetItExplicitly(): void
    {
        $this->page('checkout', 'index', 'index', '/checkout/');
        $rows = [
            $this->visibleBar(['bar_id' => 1, 'page_targeting' => 'all']),
            $this->visibleBar(['bar_id' => 2, 'page_targeting' => 'specific', 'target_page_types' => 'checkout']),
            $this->visibleBar(['bar_id' => 3, 'page_targeting' => 'exclude', 'target_page_types' => 'cart']),
            $this->visibleBar(['bar_id' => 4, 'page_targeting' => 'specific', 'target_urls' => 'checkout*']),
        ];

        $this->assertSame([2, 4], array_column($this->viewModel($rows)->getActiveBars(), 'bar_id'));
    }

    public function testIsCheckoutPage(): void
    {
        $vm = $this->viewModel();

        $this->page('checkout', 'index', 'index');
        $this->assertTrue($vm->isCheckoutPage());
        $this->page('multishipping', 'checkout', 'addresses');
        $this->assertTrue($vm->isCheckoutPage());
        $this->page('checkout', 'cart', 'index');
        $this->assertFalse($vm->isCheckoutPage());
        $this->page('cms', 'page', 'view');
        $this->assertFalse($vm->isCheckoutPage());
    }

    public function testMatchesAllPagesByDefault(): void
    {
        $vm = $this->viewModel();
        $this->assertTrue($vm->matchesCurrentPage([]));
        $this->assertTrue($vm->matchesCurrentPage(['page_targeting' => 'all', 'target_page_types' => 'cart']));
    }

    public function testSpecificTargetingWithoutCriteriaMatches(): void
    {
        $this->assertTrue($this->viewModel()->matchesCurrentPage(['page_targeting' => 'specific']));
    }

    public function testExcludeTargetingWithoutCriteriaShowsTheBar(): void
    {
        $this->assertTrue($this->viewModel()->matchesCurrentPage(['page_targeting' => 'exclude']));
    }

    public static function pageTypeProvider(): array
    {
        return [
            'home by empty path' => ['home', 'cms', 'page', 'view', '/', true],
            'homepage alias' => ['homepage', 'cms', 'index', 'index', '/home', true],
            'home not on product' => ['home', 'catalog', 'product', 'view', '/p.html', false],
            'category' => ['category', 'catalog', 'category', 'view', '/c.html', true],
            'category not product' => ['category', 'catalog', 'product', 'view', '/p.html', false],
            'product' => ['product', 'catalog', 'product', 'view', '/p.html', true],
            'cart' => ['cart', 'checkout', 'cart', 'index', '/checkout/cart', true],
            'checkout' => ['checkout', 'checkout', 'index', 'index', '/checkout', true],
            'checkout not cart' => ['checkout', 'checkout', 'cart', 'index', '/checkout/cart', false],
            'cms any page' => ['cms', 'cms', 'page', 'view', '/about-us', true],
            'search' => ['search', 'catalogsearch', 'result', 'index', '/catalogsearch/result', true],
            'account' => ['account', 'customer', 'account', 'login', '/customer/account/login', true],
            'unknown type' => ['blog', 'cms', 'page', 'view', '/x', false],
        ];
    }

    #[DataProvider('pageTypeProvider')]
    public function testSpecificPageTypes(
        string $type,
        string $module,
        string $controller,
        string $action,
        string $uri,
        bool $expected
    ): void {
        $this->page($module, $controller, $action, $uri);
        $vm = $this->viewModel();

        $this->assertSame(
            $expected,
            $vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_page_types' => $type])
        );
        $this->assertSame(
            !$expected,
            $vm->matchesCurrentPage(['page_targeting' => 'exclude', 'target_page_types' => $type])
        );
    }

    public function testPageTypeListIsTrimmed(): void
    {
        $this->page('catalog', 'product', 'view', '/p.html');
        $this->assertTrue(
            $this->viewModel()->matchesCurrentPage([
                'page_targeting' => 'specific',
                'target_page_types' => ' cart , product ',
            ])
        );
    }

    public function testUrlPatternsSupportWildcardsAndIgnoreCaseAndSlashes(): void
    {
        $this->page('catalog', 'category', 'view', '/Sale/Shoes/Boots.html?color=red');
        $vm = $this->viewModel();

        $this->assertTrue($vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_urls' => '/sale/*']));
        $this->assertTrue(
            $vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_urls' => 'about, sale/shoes/boots.html/'])
        );
        $this->assertFalse($vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_urls' => 'sale']));
        $this->assertFalse($vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_urls' => 'shoes/*']));
    }

    public function testUrlPatternDotIsLiteralNotRegex(): void
    {
        $this->page('cms', 'page', 'view', '/pageXhtml');
        $this->assertFalse(
            $this->viewModel()->matchesCurrentPage(['page_targeting' => 'specific', 'target_urls' => 'page.html'])
        );
    }

    public function testEmptyUrlPatternsAreSkipped(): void
    {
        $this->page('cms', 'page', 'view', '/about');
        $this->assertFalse(
            $this->viewModel()->matchesCurrentPage(['page_targeting' => 'specific', 'target_urls' => ' / , contact'])
        );
    }

    public function testUrlParamsMustAllMatch(): void
    {
        $this->page('cms', 'page', 'view', '/landing');
        $this->request['params'] = ['utm_source' => 'google', 'utm_medium' => 'cpc'];
        $vm = $this->viewModel();

        $this->assertTrue(
            $vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_url_params' => 'utm_source=google'])
        );
        $this->assertTrue($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_url_params' => 'utm_source = google, utm_medium=cpc',
        ]));
        $this->assertFalse($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_url_params' => 'utm_source=google,utm_medium=email',
        ]));
        $this->assertFalse(
            $vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_url_params' => 'ref=partner'])
        );
    }

    public function testUrlParamWithoutValueNeedsTheParamPresent(): void
    {
        $this->page('cms', 'page', 'view', '/landing');
        $this->request['params'] = ['utm_source' => 'google'];
        $vm = $this->viewModel();

        $this->assertTrue(
            $vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_url_params' => 'utm_source'])
        );
        $this->assertFalse(
            $vm->matchesCurrentPage(['page_targeting' => 'specific', 'target_url_params' => 'gclid'])
        );
        $this->assertTrue(
            $vm->matchesCurrentPage(['page_targeting' => 'exclude', 'target_url_params' => 'gclid'])
        );
    }

    public function testUrlParamsAcceptOnePerLineAndCommas(): void
    {
        $this->page('cms', 'page', 'view', '/landing');
        $this->request['params'] = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'gclid' => 'abc'];
        $vm = $this->viewModel();

        $this->assertTrue($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_url_params' => "utm_source=google\nutm_medium=cpc",
        ]));
        $this->assertTrue($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_url_params' => "utm_source=google\r\n\r\n utm_medium = cpc ,gclid\r\n",
        ]));
        $this->assertFalse($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_url_params' => "utm_source=google\nutm_medium=email",
        ]));
        $this->assertFalse($vm->matchesCurrentPage([
            'page_targeting' => 'exclude',
            'target_url_params' => "utm_source=google\r\ngclid",
        ]));
    }

    public function testUrlPatternsAcceptOnePerLine(): void
    {
        $this->page('catalog', 'category', 'view', '/sale/shoes.html');
        $vm = $this->viewModel();

        $this->assertTrue($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_urls' => "about\r\n/sale/*\ncontact",
        ]));
        $this->assertFalse($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_urls' => "about\ncontact",
        ]));
    }

    public function testBlankLinesOnlyCountAsNoCriteria(): void
    {
        $this->page('cms', 'page', 'view', '/landing');
        $vm = $this->viewModel();

        $this->assertTrue($vm->matchesCurrentPage([
            'page_targeting' => 'specific',
            'target_urls' => "\r\n \n",
            'target_url_params' => "\n,\r\n",
        ]));
    }

    public function testCheckoutBarTargetedByNewlineParamsIsShown(): void
    {
        $this->page('checkout', 'index', 'index', '/checkout');
        $this->request['params'] = ['promo' => 'yes'];
        $vm = $this->viewModel([
            $this->visibleBar(['bar_id' => 7, 'page_targeting' => 'specific', 'target_url_params' => "\npromo=yes\n"]),
        ]);

        $this->assertSame([7], array_column($vm->getActiveBars(), 'bar_id'));
    }

    public function testFontSizeHasAMinimumOf12AndDefaultsTo14(): void
    {
        $vm = $this->viewModel();

        $this->assertStringContainsString('font-size:12px;', $vm->getBarHtml(['font_size' => '8']));
        $this->assertStringContainsString('font-size:14px;', $vm->getBarHtml(['font_size' => '0']));
        $this->assertStringContainsString('font-size:14px;', $vm->getBarHtml([]));
        $this->assertStringContainsString('font-size:20px;', $vm->getBarHtml(['font_size' => '20']));
    }

    public function testCloseButtonIconIsHiddenFromAssistiveTech(): void
    {
        $html = $this->viewModel()->getBarHtml(['is_dismissible' => 1]);

        $this->assertStringContainsString('fill="none" aria-hidden="true" focusable="false">', $html);
    }

    public function testUrlParamValueComparisonIsStrict(): void
    {
        $this->page('cms', 'page', 'view', '/landing');
        $this->request['params'] = ['id' => ['1']];
        $this->assertFalse(
            $this->viewModel()->matchesCurrentPage(['page_targeting' => 'specific', 'target_url_params' => 'id=1'])
        );
    }

    public function testExcludeWithMatchingUrlHidesBar(): void
    {
        $this->page('cms', 'page', 'view', '/privacy-policy');
        $vm = $this->viewModel();

        $this->assertFalse($vm->matchesCurrentPage(['page_targeting' => 'exclude', 'target_urls' => 'privacy-*']));
        $this->assertTrue($vm->matchesCurrentPage(['page_targeting' => 'exclude', 'target_urls' => 'terms']));
    }

    public function testBuiltInIcons(): void
    {
        $vm = $this->viewModel();
        $names = ['info', 'warning', 'success', 'promo', 'star', 'bell', 'gift', 'truck', 'percent', 'clock'];
        foreach ($names as $name) {
            $this->assertStringStartsWith('<svg', $vm->getBuiltInIcon($name), $name);
        }
        $this->assertSame('', $vm->getBuiltInIcon('unknown'));
        $this->assertSame('', $vm->getBuiltInIcon('<script>'));
    }

    public function testBarCssIsScopedToBarAndStripsAngleBrackets(): void
    {
        $vm = $this->viewModel();

        $this->assertSame('', $vm->getBarCss(['bar_id' => 3]));
        $this->assertSame('', $vm->getBarCss(['bar_id' => 3, 'custom_css' => '   ']));
        $this->assertSame(
            '.panth-nbar[data-bar-id="3"] { color:red;/style>script>x }' . "\n",
            $vm->getBarCss(['bar_id' => '3', 'custom_css' => 'color:red;</style><script>x'])
        );
    }

    public function testBarHtmlDefaults(): void
    {
        $html = $this->viewModel()->getBarHtml(['bar_id' => 7, 'content' => 'Hello']);

        $this->assertStringContainsString('class="panth-nbar panth-nbar-pos-top_fixed"', $html);
        $this->assertStringContainsString(' role="region" aria-label="Store notification"', $html);
        $this->assertStringContainsString('data-bar-id="7"', $html);
        $this->assertStringContainsString('data-animation="slide_down"', $html);
        $this->assertStringContainsString('data-dismissible="0"', $html);
        $this->assertStringContainsString('data-cookie-duration="7"', $html);
        $this->assertStringContainsString('data-auto-close="0"', $html);
        $this->assertStringContainsString('data-date-from=""', $html);
        $this->assertStringContainsString('data-date-to=""', $html);
        $this->assertStringContainsString('data-max-bars="1"', $html);
        $this->assertStringContainsString('background-color:#1F2937;', $html);
        $this->assertStringContainsString('color:#FFFFFF;font-size:14px;padding:10px 20px;z-index:40;', $html);
        $this->assertStringContainsString('<span class="panth-nbar-text">[Hello]</span>', $html);
        $this->assertStringNotContainsString('display:none', $html);
        $this->assertStringNotContainsString('panth-nbar-close', $html);
        $this->assertStringNotContainsString('panth-nbar-cta', $html);
        $this->assertStringNotContainsString('min-height', $html);
    }

    public function testInvalidPositionAndAnimationFallBackToConfiguredDefaults(): void
    {
        $this->config['panth_notification_bar/display/default_position'] = 'bottom_floating';
        $this->config['panth_notification_bar/display/animation'] = 'fade_in';

        $html = $this->viewModel()->getBarHtml(['position' => '"><script>', 'animation' => 'spin']);

        $this->assertStringContainsString('data-position="bottom_floating"', $html);
        $this->assertStringContainsString('data-animation="fade_in"', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function testInvalidConfiguredDefaultsFallBackToHardDefaults(): void
    {
        $this->config['panth_notification_bar/display/default_position'] = 'middle';
        $this->config['panth_notification_bar/display/animation'] = 'bounce';

        $html = $this->viewModel()->getBarHtml([]);

        $this->assertStringContainsString('data-position="top_fixed"', $html);
        $this->assertStringContainsString('data-animation="slide_down"', $html);
    }

    public function testValidPositionAndAnimationAreKept(): void
    {
        $html = $this->viewModel()->getBarHtml(['position' => 'bottom_fixed', 'animation' => 'none']);

        $this->assertStringContainsString('panth-nbar-pos-bottom_fixed', $html);
        $this->assertStringContainsString('data-animation="none"', $html);
    }

    public function testStackedBarsUseConfiguredMaximum(): void
    {
        $this->flags['panth_notification_bar/display/stack_bars'] = true;
        $this->config['panth_notification_bar/general/max_visible_bars'] = '3';

        $this->assertStringContainsString('data-max-bars="3"', $this->viewModel()->getBarHtml([]));
    }

    public function testCssValuesAreSanitised(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'text_color' => 'red;background:url(javascript:alert(1))',
            'bar_padding' => '5px" onmouseover="x',
            'background_color' => '#000;}',
            'font_size' => '18',
            'bar_height' => '60',
        ]);

        $this->assertStringContainsString('color:redbackgroundurl(javascriptalert(1));', $html);
        $this->assertStringContainsString('padding:5px onmouseoverx;', $html);
        $this->assertStringContainsString('background-color:#000;', $html);
        $this->assertStringContainsString('font-size:18px;', $html);
        $this->assertStringContainsString('min-height:60px;', $html);
        $this->assertStringNotContainsString('onmouseover="', $html);
    }

    public function testGradientBackground(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'background_type' => 'gradient',
            'background_gradient' => 'linear-gradient(90deg, #111, #222);x:y',
        ]);

        $this->assertStringContainsString('style="background:linear-gradient(90deg, #111, #222)xy;', $html);
    }

    public function testEmptyGradientFallsBackToColor(): void
    {
        $html = $this->viewModel()->getBarHtml(['background_type' => 'gradient', 'background_color' => '#123456']);

        $this->assertStringContainsString('background-color:#123456;', $html);
    }

    public function testImageBackgroundStripsBreakoutCharacters(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'background_type' => 'image',
            'background_color' => '#000',
            'background_image' => "https://cdn.example.com/a b'(c);.jpg",
        ]);

        $this->assertStringContainsString(
            "background:#000 url('https://cdn.example.com/abc.jpg') center/cover no-repeat;",
            $html
        );
    }

    public function testImageBackgroundRejectsUnsafeScheme(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'background_type' => 'image',
            'background_image' => 'javascript:alert(1)',
        ]);

        $this->assertStringContainsString("url('')", $html);
        $this->assertStringNotContainsString('javascript', $html);
    }

    public function testFutureStartDateHidesBarAndExposesTimestamp(): void
    {
        $from = gmdate('Y-m-d', time() + 5 * 86400);
        $html = $this->viewModel()->getBarHtml(['date_from' => $from]);

        $expected = (string)(strtotime($from . ' 00:00:00 UTC') * 1000);
        $this->assertStringContainsString('data-date-from="' . $expected . '"', $html);
        $this->assertStringContainsString('display:none;', $html);
    }

    public function testDateToCoversTheWholeDay(): void
    {
        $today = gmdate('Y-m-d');
        $html = $this->viewModel()->getBarHtml(['date_to' => $today]);

        $expected = (string)(strtotime($today . ' 23:59:59 UTC') * 1000);
        $this->assertStringContainsString('data-date-to="' . $expected . '"', $html);
        $this->assertStringNotContainsString('display:none', $html);
    }

    public function testExplicitDateToTimeIsKept(): void
    {
        $html = $this->viewModel()->getBarHtml(['date_to' => '2001-02-03 10:00:00']);

        $this->assertStringContainsString(
            'data-date-to="' . (strtotime('2001-02-03 10:00:00 UTC') * 1000) . '"',
            $html
        );
        $this->assertStringContainsString('display:none;', $html);
    }

    public function testZeroAndInvalidDatesAreIgnored(): void
    {
        $html = $this->viewModel()->getBarHtml(['date_from' => '0000-00-00 00:00:00', 'date_to' => 'not a date']);

        $this->assertStringContainsString('data-date-from=""', $html);
        $this->assertStringContainsString('data-date-to=""', $html);
        $this->assertStringNotContainsString('display:none', $html);
    }

    public function testCountdownPlaceholderIsReplacedInBothContents(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'content' => 'Sale {countdown}',
            'mobile_content' => 'M {countdown}',
            'countdown_enabled' => 1,
            'countdown_end_date' => '2030-01-02 03:04:05',
            'countdown_label' => 'Ends <in>:',
            'countdown_expired_text' => 'Over "now"',
        ]);

        $countdown = '<span class="panth-nbar-cd-label">Ends &lt;in&gt;: </span>'
            . '<span class="panth-nbar-countdown" data-end-date="2030-01-02T03:04:05Z"'
            . ' data-expired-text="Over &quot;now&quot;"></span>';
        $this->assertStringContainsString(
            '<span class="panth-nbar-text panth-nbar-text-desktop">[Sale ' . $countdown . ']</span>',
            $html
        );
        $this->assertStringContainsString(
            '<span class="panth-nbar-text panth-nbar-text-mobile" style="display:none;">[M '
            . $countdown . ']</span>',
            $html
        );
    }

    public function testCountdownWithoutLabelUsesDefaultExpiredText(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'content' => '{countdown}',
            'countdown_enabled' => 1,
            'countdown_end_date' => '2030-01-02 03:04:05',
        ]);

        $this->assertStringNotContainsString('panth-nbar-cd-label', $html);
        $this->assertStringContainsString('data-expired-text="Expired"', $html);
    }

    public function testCountdownIsIgnoredWhenDisabledOrWithoutEndDate(): void
    {
        $vm = $this->viewModel();

        $disabled = $vm->getBarHtml(['content' => 'A {countdown}', 'countdown_end_date' => '2030-01-01']);
        $noDate = $vm->getBarHtml(['content' => 'A {countdown}', 'countdown_enabled' => 1]);

        $this->assertStringContainsString('[A {countdown}]', $disabled);
        $this->assertStringContainsString('[A {countdown}]', $noDate);
    }

    public function testBlankMobileContentRendersSingleText(): void
    {
        $html = $this->viewModel()->getBarHtml(['content' => 'Hi', 'mobile_content' => '   ']);

        $this->assertStringContainsString('<span class="panth-nbar-text">[Hi]</span>', $html);
        $this->assertStringNotContainsString('panth-nbar-text-mobile', $html);
    }

    public function testContentFilterFailureFallsBackToRawContent(): void
    {
        $this->filterThrows = true;
        $html = $this->viewModel()->getBarHtml(['content' => 'Raw {{widget}}']);

        $this->assertStringContainsString('<span class="panth-nbar-text">Raw {{widget}}</span>', $html);
    }

    public function testIconIsRenderedOnlyForKnownNames(): void
    {
        $vm = $this->viewModel();

        $this->assertStringContainsString('<span class="panth-nbar-icon" aria-hidden="true"><svg', $vm->getBarHtml(['icon' => 'gift']));
        $this->assertStringNotContainsString('panth-nbar-icon', $vm->getBarHtml(['icon' => 'nope']));
        $this->assertStringNotContainsString('panth-nbar-icon', $vm->getBarHtml(['icon' => '']));
    }

    public function testCtaIsRenderedWithEscapedTextAndNewTab(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'cta_enabled' => 1,
            'cta_text' => 'Shop <now>',
            'cta_url' => '/sale?a=1&b=2',
            'cta_open_new_tab' => 1,
            'cta_bg_color' => '#fff',
            'cta_text_color' => '#000',
        ]);

        $this->assertStringContainsString(
            '<a class="panth-nbar-cta" href="/sale?a=1&amp;b=2" target="_blank" rel="noopener noreferrer"'
            . ' style="background:#fff;color:#000;">Shop &lt;now&gt;</a>',
            $html
        );
    }

    public function testCtaRequiresEnabledFlagAndText(): void
    {
        $vm = $this->viewModel();

        $this->assertStringNotContainsString('panth-nbar-cta', $vm->getBarHtml(['cta_text' => 'Go']));
        $this->assertStringNotContainsString(
            'panth-nbar-cta',
            $vm->getBarHtml(['cta_enabled' => 1, 'cta_text' => ''])
        );
    }

    public static function unsafeUrlProvider(): array
    {
        return [
            'javascript' => ['javascript:alert(1)'],
            'mixed case' => ['JaVaScRiPt:alert(1)'],
            'whitespace obfuscated' => ["java\tscript:alert(1)"],
            'leading control char' => [" \x01javascript:alert(1)"],
            'data uri' => ['data:text/html;base64,AAAA'],
            'vbscript' => ['vbscript:msgbox(1)'],
            'empty' => ['   '],
        ];
    }

    #[DataProvider('unsafeUrlProvider')]
    public function testUnsafeCtaUrlsBecomeHash(string $url): void
    {
        $html = $this->viewModel()->getBarHtml(['cta_enabled' => 1, 'cta_text' => 'Go', 'cta_url' => $url]);

        $this->assertStringContainsString('<a class="panth-nbar-cta" href="#"', $html);
    }

    public static function safeUrlProvider(): array
    {
        return [
            ['https://example.com/x'],
            ['http://example.com'],
            ['mailto:a@example.com'],
            ['tel:+123'],
            ['/relative/path'],
            ['#anchor'],
        ];
    }

    #[DataProvider('safeUrlProvider')]
    public function testSafeCtaUrlsAreKept(string $url): void
    {
        $html = $this->viewModel()->getBarHtml(['cta_enabled' => 1, 'cta_text' => 'Go', 'cta_url' => $url]);

        $this->assertStringContainsString('href="' . htmlspecialchars($url, ENT_QUOTES) . '"', $html);
        $this->assertStringNotContainsString('target="_blank"', $html);
    }

    public function testDismissibleBarHasCloseButton(): void
    {
        $html = $this->viewModel()->getBarHtml([
            'is_dismissible' => 1,
            'cookie_duration' => '30',
            'auto_close_seconds' => 5,
        ]);

        $this->assertStringContainsString('data-dismissible="1"', $html);
        $this->assertStringContainsString('data-cookie-duration="30"', $html);
        $this->assertStringContainsString('data-auto-close="5"', $html);
        $this->assertStringContainsString(
            '<button type="button" class="panth-nbar-close" aria-label="Dismiss notification">',
            $html
        );
    }

    public function testDeviceFlagsAreExposed(): void
    {
        $html = $this->viewModel()->getBarHtml(['show_on_mobile' => 1, 'show_on_desktop' => 0]);

        $this->assertStringContainsString('data-show-mobile="1"', $html);
        $this->assertStringContainsString('data-show-desktop="0"', $html);
    }
}
