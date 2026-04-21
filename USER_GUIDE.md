# Panth Notification Bar - User Guide

## Overview

Panth Notification Bar lets you run unlimited announcement / promo bars on your Magento 2 storefront — with positions, stacking, countdown timers, CTA buttons, scheduling, granular targeting (page, customer group, country, device), and dismissible cookie memory. A single module replaces every "top bar", "free shipping bar", "flash sale strip", and "cookie notice" you previously hard-coded.

## Getting Started

After installation, navigate to **Panth Extensions → Notification Bar** in the Magento admin.

## Global Settings

**Stores → Configuration → Panth Extensions → Notification Bar**

### General

- **Enable Notification Bar** — master switch for the module.
- **Max Visible Bars** — the maximum number of bars rendered on any page at once (default: 3).

### Display

- **Default Position** — fallback position used when creating a new bar.
- **Stack Multiple Bars** — when enabled, all qualifying bars render together in priority order; when disabled, only the highest-priority bar is shown.
- **Default Animation** — `slide_down`, `fade_in`, or `none`.
- **Z-Index** — CSS stacking for the bar container (default: 9999).

## Managing Bars

### Create a Bar

1. Go to **Panth Extensions → Notification Bar → Manage Bars**
2. Click **Add New Bar**
3. Fill in the Core fields:
   - **Name** — internal admin name
   - **Is Active** — enable / disable this bar
   - **Bar Type** — `info`, `warning`, `success`, `promo`, `urgent`, `custom`
   - **Position** — `top_fixed`, `top_static`, `bottom_fixed`, `bottom_floating`
   - **Sort Order** — priority for stacking (lower renders first)
   - **Content** — HTML bar message
4. Appearance:
   - **Background Type** — solid **color**, **gradient** (CSS), or **image**
   - **Background Color / Gradient / Image** — matching fields by type
   - **Text Color**, **Font Size**, **Bar Height** (0 = auto), **Bar Padding**
   - **Icon** — optional icon name
   - **Custom CSS** — per-bar CSS override
5. CTA Button (optional):
   - **CTA Enabled**, **CTA Text**, **CTA URL**
   - **CTA Open New Tab**
   - **CTA Background Color**, **CTA Text Color**
6. Countdown (optional):
   - **Countdown Enabled**
   - **Countdown End Date** (datetime)
   - **Countdown Label** — text shown before the timer
   - **Countdown Expired Text** — fallback after zero
7. Dismissal:
   - **Is Dismissible** — show the × button
   - **Cookie Duration** — days to remember dismissal (`0` = session)
8. Animation:
   - **Animation** — `slide_down`, `fade_in`, `none`
   - **Auto Close Seconds** — seconds before auto-close (`0` = never)
9. Scheduling:
   - **Active From Date / Active To Date** — bars render only inside this window
10. Targeting:
    - **Store IDs** — comma-separated (or `0` = all)
    - **Customer Groups** — comma-separated group IDs
    - **Page Targeting** — `all`, `specific`, or `exclude`
    - **Target URL Patterns** — comma-separated, `*` wildcard supported
    - **Target Page Types** — comma-separated layout handles
    - **Target Countries** — ISO country codes
    - **Target URL Params** — `key=value` pairs
11. Device:
    - **Show on Mobile**, **Show on Desktop**
    - **Mobile Content** — optional shorter copy for small screens
12. Click **Save Bar**

### Edit or Delete a Bar

Use the standard admin grid at **Manage Bars** — inline edit, mass actions (enable / disable / delete), filters, and column config are all supported.

## Positions

| Position | Behavior |
|---|---|
| `top_fixed` | Sticky bar pinned to the top of the viewport |
| `top_static` | Top of the page, scrolls away with content |
| `bottom_fixed` | Sticky bar pinned to the bottom of the viewport |
| `bottom_floating` | Floating pill at the bottom, rounded corners |

## Stacking

- When **Stack Multiple Bars** is enabled (global setting), all qualifying bars render together in **Sort Order** ascending (lowest first), capped at **Max Visible Bars**.
- When **Stack Multiple Bars** is disabled, only the single highest-priority qualifying bar is shown.

## Countdown Timers

Any bar can include a live client-side countdown:

1. Enable **Countdown**
2. Set **Countdown End Date** (store timezone)
3. Optionally add a **Countdown Label** prefix (e.g. *"Flash sale ends in"*)
4. Optionally add **Countdown Expired Text** (e.g. *"Sale has ended."*)

The counter renders `days : hours : minutes : seconds` and updates every second. On expiry, the bar either shows the expired text or self-closes if **Auto Close Seconds** is configured.

## Targeting Recipes

**EU cookie notice**
- Target Countries: `AT,BE,BG,CY,CZ,DE,DK,EE,ES,FI,FR,GR,HR,HU,IE,IT,LT,LU,LV,MT,NL,PL,PT,RO,SE,SI,SK`
- Position: `bottom_fixed`
- Is Dismissible: Yes, Cookie Duration: 365

**VIP-only promo**
- Customer Groups: `3` (your VIP group ID)
- Bar Type: `promo`
- CTA: **Shop VIP Collection** → `/vip`

**Free shipping threshold**
- Position: `top_static`
- Target Page Types: `cms_index_index,catalog_category_view,catalog_product_view`
- Content: `Free shipping on orders over $50`

**Flash sale with countdown**
- Bar Type: `urgent`
- Active From / To: sale window
- Countdown: on, end date = sale end
- Auto Close Seconds: 5 (after countdown hits zero, bar fades out)

**UTM campaign bar**
- Target URL Params: `utm_campaign=spring2026`
- Bar Type: `promo`
- CTA: **Shop the Spring Sale** → sale landing page

## Configuration Options Cheat Sheet

| Option | Default | Description |
|---|---|---|
| Is Active | Yes | Enable / disable this bar |
| Bar Type | info | Preset color palette |
| Position | top_fixed | Where the bar renders |
| Sort Order | 0 | Priority for stacking (lower first) |
| Background Type | color | color / gradient / image |
| Background Color | #1F2937 | Solid color fallback |
| Text Color | #FFFFFF | Body text color |
| Font Size | 14px | Body font size |
| Bar Height | 0 | `0` = auto |
| Bar Padding | 10px 20px | CSS padding shorthand |
| CTA Enabled | No | Show a CTA button |
| Countdown Enabled | No | Show a live countdown |
| Is Dismissible | Yes | Show the × button |
| Cookie Duration | 7 | Days the dismissal is remembered |
| Animation | slide_down | Entry animation |
| Auto Close Seconds | 0 | `0` = never auto-close |
| Show on Mobile | Yes | Render on mobile widths |
| Show on Desktop | Yes | Render on desktop widths |

## CSS Customization

Preset palettes per `bar_type` live in `etc/theme-config.json`. Override from your theme's CSS using standard CSS custom properties:

```css
:root {
    --notification-bar-z-index: 9999;
    --notification-bar-font-size: 14px;
    --notification-bar-padding: 10px 20px;
    --notification-bar-transition: 300ms ease-out;
    --notification-bar-cta-radius: 4px;
}
```

Per-bar **Custom CSS** is injected inline for one-off designs.

## Troubleshooting

### Bar not displaying

- Confirm **Stores → Configuration → Panth Extensions → Notification Bar → Enabled = Yes**
- Confirm the bar itself is **Active**
- Re-check audience: store, customer group, country, page targeting, URL params
- Re-check schedule: **Active From / Active To**
- Clear cookies if you previously dismissed the bar
- Flush Magento cache

### Bar flashes and disappears

- Another bar with higher sort order on the same position is taking over
- Enable **Stack Multiple Bars** or lower the priority bar's **Sort Order**

### Countdown always shows expired text

- **Countdown End Date** is in the past — update it
- Server timezone differs from store timezone — verify in Stores → Configuration → General

### Bar shows on checkout when not wanted

- Change **Page Targeting** to `exclude`
- Add `/checkout/*`, `/onestepcheckout/*`, `/customer/account/*` to **Target URL Patterns**

### Styling conflicts with fixed header

- Increase the global **Z-Index** or add per-bar **Custom CSS**

### Bar not visible after theme switch

- Run `bin/magento cache:flush`
- Run `bin/magento setup:static-content:deploy -f`

### Module status check

```bash
bin/magento module:status Panth_NotificationBar
# Expected: Module is enabled
```
