# Changelog

## [1.0.8]

### Changed
- Code cleanup: removed redundant inline comments and docblocks from the PHP source. No functional changes.

## [1.0.7] - 2026-06-18

### Changed
- Rewrote README to match the Panth module standard: added Quick Answer block, Who Is It For section, How It Works section, expanded FAQ, Quick Links table, SEO keywords, and updated canonical URL to the live product page.

## [1.0.6] - 2026-06-14

### Fixed
- PHP 8.4 / Magento 2.4.9 compatibility: made the grid collection signatures in `Model/ResourceModel/Bar/Grid/Collection.php` explicitly nullable (`?AdapterInterface $connection`, `?AbstractDb $resource`, `?SearchCriteriaInterface $searchCriteria`, `?array $items`). PHP 8.4 deprecates implicit-nullable parameters and Magento's compiler promotes that to an error, which broke `setup:di:compile`. Behaviour is unchanged.

## [1.0.4] - 2026-05-15

### Fixed
- `getCookie()` in `view/frontend/templates/notification-bar.phtml` rewritten to use a single-capture regex with a non-capturing prefix group, so the dismissal check correctly reads the cookie value. Dismissed bars now stay hidden across pageviews when `cookie_duration > 0`.

## [1.0.1] - 2026-04-21

### Added
- Comprehensive README, USER_GUIDE, CHANGELOG, LICENSE, COPYING, and .gitattributes for marketplace + Packagist listings.

## [1.0.0] - 2026-04-17

### Added
- Unlimited notification / announcement bars with full admin CRUD
- Four positions: `top_fixed`, `top_static`, `bottom_fixed`, `bottom_floating`
- Six bar types with preset theme colors: `info`, `warning`, `success`, `promo`, `urgent`, `custom`
- Rich HTML content with optional mobile-specific override
- Three background types: solid color, gradient CSS, uploaded image
- Full typography controls: text color, font size, bar height, padding, icon, custom CSS
- Optional CTA button per bar with label, URL, new-tab flag, and background / text colors
- Live client-side countdown timer with custom label and expired-text fallback
- Dismissible toggle with configurable cookie duration (days; `0` = session)
- Stacking engine with global `max_visible_bars` cap and per-bar sort order
- Active-from / active-to scheduling (server-filtered, no layout shift)
- Entry animations: `slide_down`, `fade_in`, `none`
- Auto-close seconds for self-dismissing bars
- Store view targeting (comma-separated store IDs, `0` = all)
- Customer group targeting (comma-separated group IDs)
- Country targeting (ISO country codes)
- Page targeting modes: `all`, `specific`, `exclude`
- URL pattern targeting with `*` wildcard
- Page type targeting (layout handles)
- URL parameter targeting (`key=value` pairs for UTM / campaign filtering)
- Device targeting: show on mobile / desktop independently
- Global configuration section under **Panth Extensions → Notification Bar**
- Unified admin menu entry under **Panth Infotech → Notification Bar**
- Hyva theme support (Alpine.js template)
- Luma theme support (vanilla JS template)
- Automatic theme detection via `Panth\Core\Helper\Theme`
- `ifconfig`-gated block — no build cost when the module is disabled
- Single indexed database table (`panth_notification_bar`) with indexes on `is_active`, `sort_order`, `position`, `date_from`, `date_to`, `bar_type`
- ACL resources for granular admin permissions (`Panth_NotificationBar::manage`, `Panth_NotificationBar::config`)
