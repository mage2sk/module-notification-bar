# Changelog

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
