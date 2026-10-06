# Magento 2 Notification Bar

Panth Notification Bar adds admin-managed announcement bars to a Magento 2 storefront. Each bar is a database record with its own content, position, colours, optional call-to-action button, optional countdown timer, schedule, store view, customer group, page and device rules, and dismissal behaviour. The module renders the qualifying bars in the `after.body.start` container on every frontend page, so nothing in the theme has to be edited.

It is used by store owners and marketing teams who want to publish promotions, shipping notices, cookie notices or similar messages from the admin panel. The single template uses plain JavaScript (no jQuery, Alpine.js, Knockout or RequireJS) and is used on both Hyva and Luma themes.

Product page: [kishansavaliya.com/magento-2-notification-bar.html](https://kishansavaliya.com/magento-2-notification-bar.html)

## Features

- Any number of bars managed in an admin grid with an add/edit form, inline edit, and mass Enable, Disable and Delete actions.
- Keyword search on the bars grid (name, CTA text, content).
- Four positions per bar: "Top Fixed", "Top Static", "Bottom Fixed" and "Bottom Floating".
- Bars render in "Sort Order" ascending, capped by the global "Max Visible Bars" setting (one bar when "Stack Multiple Bars" is No).
- HTML content per bar, processed by the CMS block filter so directives such as `{{store url=""}}` and `{{media url=""}}` work, with an optional separate "Mobile Content" used on mobile devices (mobile user agent or a viewport narrower than 768 px).
- Background as a solid colour, a CSS gradient or an image; per-bar text colour, font size, bar height, padding and "Custom CSS".
- Ten built-in inline SVG icons: `info`, `warning`, `success`, `promo`, `star`, `bell`, `gift`, `truck`, `percent`, `clock`.
- Optional call-to-action button with text, URL, "Open in New Tab" and its own background and text colours. Only relative URLs and the `http`, `https`, `mailto` and `tel` schemes are rendered; any other scheme is replaced with `#`.
- Optional client-side countdown timer inserted where the `{countdown}` placeholder appears in the content, with a label and an expired-text fallback.
- Scheduling with "From Date" and "To Date" (whole days in the store view's timezone). The window is also checked in the browser, so bars start and stop on time on pages served from the full page cache.
- Targeting by store view, customer group, page type, URL pattern (with `*` wildcard) and URL parameters (`key=value`), in "All Pages", "Specific Pages Only" or "All Pages Except" mode.
- Device rules: "Show on Mobile" and "Show on Desktop".
- Dismissible bars with a close button; the dismissal is remembered in a cookie for "Cookie Duration (Days)", or in `sessionStorage` when the duration is 0.
- "Auto-close After (Seconds)" hides a bar automatically.
- Entry animation per bar: "Slide Down", "Fade In" or "None".
- Console command that inserts five sample bars.
- The frontend block is gated by `ifconfig`, so nothing is rendered when the module is disabled in configuration.

## Compatibility

| Platform | Versions |
|---|---|
| Magento Open Source | 2.4.4 to 2.4.8 (as published on the product page) |
| Adobe Commerce | 2.4.4 to 2.4.8 (as published on the product page) |
| PHP | ~8.1.0, ~8.2.0, ~8.3.0, ~8.4.0 (from `composer.json`) |
| Themes | Hyva and Luma (one plain-JavaScript template is used for both) |

Composer constraints on Magento packages: `magento/framework` `^103.0`, `magento/module-store` `^101.1`, `magento/module-cms` `^104.0`, `magento/module-customer` `^103.0`, `magento/module-backend` `^102.0`, `magento/module-ui` `^101.2`, `magento/module-config` `^101.2`, `magento/module-directory` `^100.4`.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.4 to 2.4.8
- PHP 8.1, 8.2, 8.3 or 8.4
- `mage2kishan/module-core` `^1.0` (module `Panth_Core`), which provides the "Panth Extensions" admin menu group and the ACL parent resource `Panth_Core::panth_extensions`
- Magento modules `Magento_Store`, `Magento_Cms`, `Magento_Customer`, `Magento_Backend`, `Magento_Ui`, `Magento_Config` and `Magento_Directory` (declared in `composer.json`)

## Installation

```bash
composer require mage2kishan/module-notification-bar
bin/magento module:enable Panth_Core Panth_NotificationBar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

`setup:di:compile` is only required in production mode. The module ships no files under `view/*/web`, so no static content deployment is needed for it.

Check the result:

```bash
bin/magento module:status Panth_NotificationBar
```

## Configuration

Admin path: Stores > Configuration > Panth Extensions > Notification Bar. The section can be set at default, website and store view scope. It is also linked from the admin menu entry Notification Bar > Configuration.

### General Settings

| Setting | Default | What it does |
|---|---|---|
| Enable Notification Bar | Yes | Master switch. When set to No the frontend block is not rendered at all. |
| Max Visible Bars | 3 | Maximum number of bars rendered on one page. Qualifying bars are taken in sort order until this number is reached. If the value is 0 or empty the code falls back to 5. |

### Display Settings

| Setting | Default | What it does |
|---|---|---|
| Default Position | Top Fixed | Used on the storefront when a bar has no valid position of its own. The bar form does not read this value. |
| Stack Multiple Bars | Yes | When No, only the first qualifying bar (lowest sort order) is shown. When Yes, bars are shown up to "Max Visible Bars". |
| Default Animation | Slide Down | Used on the storefront when a bar has no valid animation of its own. |
| Z-Index | 40 | CSS `z-index` of each bar. 0 or empty falls back to 40, which keeps bars below the sticky header (50), drawers (70) and modals (80). |

Configuration paths:

- `panth_notification_bar/general/enabled`
- `panth_notification_bar/general/max_visible_bars`
- `panth_notification_bar/display/default_position`
- `panth_notification_bar/display/stack_bars`
- `panth_notification_bar/display/animation`
- `panth_notification_bar/display/z_index`

Default behaviour after installation: the module is enabled, but no bars exist, so nothing is shown on the storefront until a bar is created (or the sample-data command is run).

### Managing bars

Admin menu: Notification Bar > Manage Bars (added under the `Panth_Core::panth_extensions` menu group, admin route `panth_notificationbar/bar/index`). The grid lists ID, Name, Bar Type, Position, Active, Sort Order, Date From, Date To and Created At, with filters, column controls, bookmarks and mass actions Delete, Enable and Disable. The "Add New Bar" button opens the form, which has these fieldsets:

- General: Name, Active, Bar Type ("Info", "Warning", "Success", "Promo", "Urgent", "Custom"), Sort Order, Position.
- Content & Appearance: Content, Mobile Content, Background Type ("Color", "Gradient", "Image"), Background Color, Background Gradient, Background Image URL, Text Color, Font Size (px, 12 to 48), Minimum Bar Height (px, 0 = automatic), Bar Padding, Icon, Animation, Custom CSS.
- Call to Action Button: Enable CTA Button, Button Text, Button URL, Open in New Tab, Button Background Color, Button Text Color.
- Countdown Timer: Enable Countdown, Countdown End Date, Countdown Label, Countdown Expired Text.
- Targeting & Schedule: Store Views, Customer Groups, From Date, To Date, Page Targeting, Target Page Types, Target URLs, Target Countries, Target URL Parameters.
- Dismissal & Auto-close: Is Dismissible, Cookie Duration (Days), Auto-close After (Seconds), Show on Mobile, Show on Desktop.

Multiselect values (Store Views, Customer Groups, Target Page Types, Target Countries) are stored as comma-separated lists in the database. "Bar Type" and "Target Countries" are stored and shown in the admin, but the storefront code in this version does not change styling based on the bar type and does not filter bars by the visitor's country.

## Usage

### Storefront output

The layout file `view/frontend/layout/default.xml` adds the block `panth.notification.bar` (template `Panth_NotificationBar::notification-bar.phtml`) to the `after.body.start` container on every page, before any other child of that container. The template outputs one `<style>` block, one `<div class="panth-nbar ...">` per bar and one inline `<script>`; it returns nothing when the module is disabled or no bar qualifies.

For each page request the view model loads bars that are active and whose "To Date" is today or later (store view timezone), ordered by sort order, then drops any bar that fails one of these checks:

- Store Views: the bar's list must be empty, contain `0`, or contain the current store ID.
- Customer Groups: the list must be empty or contain the current customer group ID (guests use group 0).
- Page Targeting: with "All Pages" every page qualifies. With "Specific Pages Only" the page must match one of the bar's page types (`home`, `cms`, `category`, `product`, `cart`, `checkout`, `search`, `account`), URL patterns (matched against the request path, case-insensitive, `*` as wildcard) or URL parameters (all listed `key=value` pairs must be present; a bare `key` only needs that parameter in the URL). Target URLs and Target URL Parameters take one entry per line or comma-separated entries. "All Pages Except" inverts that result. A bar with no page types, URLs or parameters matches every page in both modes.
- Checkout: on the checkout page (`checkout_index_*`) and the multishipping checkout steps only bars set to "Specific Pages Only" with page types, URL patterns or URL parameters that match the page are shown; "All Pages" and "All Pages Except" bars are skipped there.

The inline script then checks each rendered bar in the browser, so the result stays correct on pages served from the full page cache:

- Schedule: "From Date" starts at 00:00:00 and "To Date" ends at 23:59:59 of that day in the store view's timezone. Bars outside that window are hidden.
- Device: a visitor counts as mobile when `navigator.userAgent` matches `Mobile`, `Android`, `iPhone`, `iPad`, `iPod`, `Opera Mini`, `IEMobile` or `WPDesktop`, or when the viewport is narrower than 768 px; the bar must allow the detected device. When "Mobile Content" is filled it is shown instead of the main content on mobile devices, and on desktop browsers the two texts swap when the window is resized across 768 px.
- Bars are counted in sort order up to "Max Visible Bars" (one when "Stack Multiple Bars" is No); later bars are hidden. Bars the visitor dismissed, bars outside their dates and bars hidden for the device do not count.
- Several bottom bars stack upwards from the bottom edge and several Top Fixed bars stack downwards; the page gets matching top or bottom padding so fixed bars do not cover the header or the footer.

Saving or deleting a bar in the admin cleans the full page cache entries that contain the notification bar block (cache tag `panth_nbar`).

Positions: `top_fixed` and `bottom_fixed` use `position: fixed` at the top or bottom of the viewport; `top_static` is rendered in the document flow; `bottom_floating` is fixed 20px from the bottom and sides with rounded corners and a shadow.

### Countdown timer

Place `{countdown}` in the bar content and enable the countdown with an end date. The placeholder is replaced by a live counter (days, hours, minutes, seconds) updated every second in the browser; after the end date the label is hidden and the "Countdown Expired Text" is shown as plain text (`Expired` when the field was never set); when the field is empty the whole bar hides at the end date. The end date is entered in the configured store timezone, stored in UTC and sent to the browser as an ISO 8601 UTC value, so every visitor sees the same end moment. Without the placeholder no counter appears.

### Call-to-action button

The button is rendered after the text when "Enable CTA Button" is Yes and "Button Text" is not empty. With "Open in New Tab" the link gets `target="_blank" rel="noopener noreferrer"`. A button whose URL is `#` on a dismissible bar acts as a dismiss button (used by the sample "Cookie Consent" bar).

### Dismissal

The close button writes the cookie `panth_nbar_<bar_id>=dismissed` for "Cookie Duration (Days)" days; with a duration of 0 the flag is written to `sessionStorage` instead. On later page loads the script hides a bar whose flag is present. "Auto-close After (Seconds)" dismisses the bar after that many seconds; on a dismissible bar this also stores the flag.

### Console command

```bash
bin/magento panth:notificationbar:install-sample-data
```

Inserts five sample bars into `panth_notification_bar`: "Welcome Promo Banner", "Free Shipping Notice", "Flash Sale Countdown" (countdown ending in three days), "Cookie Consent" and "Holiday Hours" (inactive). Running the command again inserts the rows again. Flush the cache afterwards to see the bars.

### Overriding the template

Copy `view/frontend/templates/notification-bar.phtml` to `app/design/frontend/<Vendor>/<theme>/Panth_NotificationBar/templates/notification-bar.phtml`. The HTML of each bar is produced by `ViewModel\NotificationBar::getBarHtml()`, and the CSS uses the class names `panth-nbar`, `panth-nbar-pos-<position>`, `panth-nbar-content`, `panth-nbar-icon`, `panth-nbar-text`, `panth-nbar-cta`, `panth-nbar-close`, `panth-nbar-countdown`, `panth-nbar-cd-label` and `panth-nbar-cd-unit`. The font family can be set with the CSS custom property `--panth-nbar-font`.

There are no cron jobs, observers, plugins, web API endpoints or widgets in this module.

## Developer Notes

- Module name: `Panth_NotificationBar`; Composer package: `mage2kishan/module-notification-bar`; PSR-4 namespace: `Panth\NotificationBar`.
- `ViewModel\NotificationBar` (implements `ArgumentInterface`): `isEnabled()`, `getActiveBars()`, `getBarHtml(array $bar)`, `getBarCss(array $bar)`, `matchesCurrentPage(array $bar)`, `getBuiltInIcon(string $name)`, `getMaxVisibleBars()`, `getZIndex()`.
- `Block\NotificationBar` (the frontend block class, adds the cache tag `panth_nbar` to the page).
- `Model\Bar` (event prefix `panth_notification_bar`, implements `IdentityInterface` with cache tags `panth_nbar` and `panth_nbar_<bar_id>`) with typed getters and setters for every column, plus `getStoreIdsArray()`, `getCustomerGroupsArray()`, `getTargetCountriesArray()`, `getTargetPageTypesArray()`, `getTargetUrlsArray()`.
- `Model\ResourceModel\Bar` (table constant `TABLE_NAME`), `Model\ResourceModel\Bar\Collection` with `addActiveFilter()`, `addStoreFilter(int $storeId)`, `addPositionFilter(string $position)`, and `Model\ResourceModel\Bar\Grid\Collection` registered as `panth_notification_bar_listing_data_source`.
- `Model\Bar\DataProvider` for the form; option sources in `Model\Config\Source` (`Animation`, `BarType`, `Countries`, `CustomerGroups`, `PageTargeting`, `PageTypes`, `Position`).
- Admin controllers under `Controller\Adminhtml\Bar`: `Index`, `NewAction`, `Edit`, `Save`, `Delete`, `InlineEdit`, `MassDelete`, `MassStatus` (route `panth_notificationbar`, all using ACL `Panth_NotificationBar::manage`).
- UI components: `panth_notification_bar_listing` and `panth_notification_bar_form`; form buttons in `Block\Adminhtml\Bar\Edit`; actions column `Ui\Component\Listing\Column\BarActions`.
- Console command class: `Console\Command\InstallSampleDataCommand`.
- DI: registers `Panth_NotificationBar` in the `registeredModules` argument of `Panth\Core\ViewModel\ThemeConfig` (`etc/di.xml` and `etc/frontend/di.xml`); `etc/theme-config.json` holds background and text colour values for the `info`, `success`, `warning`, `promo` and `urgent` bar types.
- ACL resources (under `Panth_Core::panth_extensions`): `Panth_NotificationBar::manage` ("Notification Bar - Manage Bars") and `Panth_NotificationBar::config` ("Notification Bar - Configuration").
- Database: table `panth_notification_bar` (primary key `bar_id`, indexes on `is_active`, `sort_order`, `position`, `date_from`, `date_to`, `bar_type`), declared in `etc/db_schema.xml`.

## Uninstallation

```bash
bin/magento module:disable Panth_NotificationBar
composer remove mage2kishan/module-notification-bar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

The table `panth_notification_bar` and the `panth_notification_bar/*` rows in `core_config_data` are not removed by these steps; drop or delete them manually if they are no longer needed. `Panth_Core` stays installed if other Panth modules use it.

## Support

- Product page: [kishansavaliya.com/magento-2-notification-bar.html](https://kishansavaliya.com/magento-2-notification-bar.html)
- Contact: [kishansavaliya.com/contact](https://kishansavaliya.com/contact)
- Email: kishansavaliyakb@gmail.com
- GitHub issues: [github.com/mage2sk/module-notification-bar/issues](https://github.com/mage2sk/module-notification-bar/issues)

## Documentation

[USER_GUIDE.md](USER_GUIDE.md) covers the global settings, creating and editing bars, positions, stacking, countdown timers, targeting recipes, a per-bar option cheat sheet, CSS customisation and troubleshooting.

## License

Commercial software license. See [LICENSE.txt](LICENSE.txt) in this repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Links

- Website: [kishansavaliya.com](https://kishansavaliya.com)
- All extensions: [kishansavaliya.com/magento-extensions.html](https://kishansavaliya.com/magento-extensions.html)
- GitHub: [github.com/mage2sk/module-notification-bar](https://github.com/mage2sk/module-notification-bar)
- Packagist: [packagist.org/packages/mage2kishan/module-notification-bar](https://packagist.org/packages/mage2kishan/module-notification-bar)

## Bottom bar offset

While a bottom fixed or bottom floating bar is visible, the module sets the CSS variable `--panth-bottom-bar-offset` (the distance from the bottom of the viewport to the top of the highest bottom bar) and the class `panth-has-bottom-bar` on the root element. Floating buttons and toasts can add the variable to their `bottom` offset to stay above the bar; the WhatsApp and Live Activity modules do this. The page body gets the same bottom padding so its last controls can be scrolled clear of the bar, and bottom bars are not shown on the checkout or multishipping checkout pages.
