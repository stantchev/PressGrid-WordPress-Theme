# PressGrid — WordPress Theme

> A production-ready, security-hardened WordPress theme engineered for high-traffic news and magazine websites.

[![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-blue?logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Lighthouse](https://img.shields.io/badge/Lighthouse-95%2B-success)](https://pagespeed.web.dev)
[![WP.org Compliant](https://img.shields.io/badge/WP.org-Compliant-blue)](https://make.wordpress.org/themes/handbook/review/)
[![Version](https://img.shields.io/badge/Version-2.5.0-orange)](https://github.com/stantchev/PressGrid-WordPress-Theme/releases)

---

## ✦ Overview

**PressGrid** is a fully open-source WordPress theme built for the demands of modern news operations. It ships with a visual Layout Builder, 6 advertisement zones, a secure font upload system, CSS variable theming, OpenWeatherMap weather integration, Frankfurter ECB forex ticker, and full WordPress.org Theme Review compliance — all with zero JavaScript dependencies beyond WordPress core.

📖 **[Full Documentation →](https://stantchev.github.io/PressGrid-WordPress-Theme/)**
🔑 **[API Setup Guide →](API-GUIDE.md)**

---

## ⚡ Key Features

| Category | Details |
|---|---|
| **Performance** | Lighthouse 95+, deferred JS, lazy images, `no_found_rows`, Redis-compatible transient cache, preconnect hints |
| **Security** | Nonces, capability checks, WOFF2 MIME validation, security headers, author enumeration block |
| **Layout Builder** | 8 configurable homepage sections with enable/disable, layout type, category, and post count |
| **Ad System** | 6 ad zones with mobile/desktop visibility, async load, and `wp_kses()` sanitization |
| **Customizer** | CSS custom property theming with live postMessage preview — 6 color controls |
| **Typography** | Secure `.woff2` font upload (MIME-validated, 1MB cap, random rename, `.htaccess` protected) |
| **Weather** | OpenWeatherMap widget — sidebar + top bar mini widget, 5-day forecast, geolocation, Bulgarian UI |
| **Forex Ticker** | Frankfurter ECB rates — auto-activates when a Business/Finance section is present in Layout Builder |
| **Reading UX** | Progress bar, estimated read time, native share buttons, related posts, back-to-top button |
| **SEO** | NewsArticle schema, Open Graph, Twitter Cards, breadcrumbs, canonical tags |
| **Accessibility** | WCAG 2.1 AA — skip links, ARIA labels, focus-visible, semantic HTML5 |
| **Compatibility** | WP Super Cache, W3TC, LiteSpeed, WP Rocket, Redis, Memcached |
| **Standards** | GPL-2.0+, PHPCS WordPress Coding Standards, WP.org Theme Review compliant |

---

## 📦 GitHub Repository Info

**Repo Name:**
```
pressgrid
```

**Repo Description:**
```
Production-ready WordPress theme for high-traffic news & magazine websites. Layout Builder, 6 Ad Zones, OpenWeatherMap, Frankfurter forex ticker, CSS variable Customizer, security-hardened, Lighthouse 95+, vanilla JS, GPL licensed.
```

**Topics / Tags:**
```
wordpress wordpress-theme news-theme magazine-theme layout-builder
customizer advertisement-zones performance security-hardened
vanilla-js gpl lighthouse accessibility wcag schema-org
openweathermap frankfurter forex weather
```

---

## 🗂️ File Structure

```
pressgrid/
├── style.css                   # Theme header + complete CSS (~22KB)
├── functions.php               # Setup, enqueue, transients, SEO, schema, preconnect
├── header.php                  # Sticky nav, breaking ticker / forex ticker, logo
├── footer.php                  # Widgets, nav, developer credit
├── index.php                   # Fallback archive template
├── front-page.php              # Layout Builder homepage
├── single.php                  # Single post — progress bar, share, related posts
├── archive.php                 # Category / tag / author archives
├── page.php                    # Static pages
├── search.php                  # Search results
├── comments.php                # Threaded comments + form
├── sidebar.php                 # Sticky sidebar widget area
├── 404.php                     # Custom 404
├── API-GUIDE.md                # API setup guide (Google Fonts, OWM, Frankfurter)
│
├── inc/
│   ├── security.php            # Headers, XMLRPC toggle, font upload security
│   ├── customizer.php          # All Customizer controls + CSS variable output
│   ├── typography.php          # Secure .woff2 upload admin page
│   ├── ads.php                 # 6 ad zones, wp_kses sanitization, rendering
│   ├── layout-builder.php      # Layout Builder + Security settings admin pages
│   ├── weather.php             # OpenWeatherMap integration + Customizer settings
│   └── forex.php               # Frankfurter ECB forex ticker + business section detection
│
├── template-parts/
│   ├── content/
│   │   ├── post-card.php       # Reusable post card component (with hover animation)
│   │   └── none.php            # No results state
│   └── layout/
│       ├── hero.php            # Hero grid (1 large + sidebar cards)
│       ├── latest-posts.php    # Latest posts — grid-2/3/4 or list
│       ├── category-grid.php   # Category grid (transient cached)
│       ├── trending.php        # Trending by comment count (cached)
│       ├── editor-picks.php    # Editor picks random (cached 30min)
│       └── newsletter.php      # Newsletter CTA section
│
├── assets/js/
│   ├── main.js                 # Nav, lazy-load, progress bar, share, back-to-top (deferred)
│   └── customizer-preview.js  # Live CSS variable Customizer preview
│
├── languages/
│   ├── pressgrid.pot           # Translation template (182 strings)
│   ├── pressgrid-bg_BG.po/mo  # Bulgarian translation
│   └── pressgrid-en_GB.po/mo  # English (UK)
│
├── screenshot.png              # 880×660 theme screenshot
└── readme.txt                  # WordPress.org compliant readme
```

---

## 🚀 Installation

### Option 1 — WordPress Admin

1. Go to **Appearance → Themes → Add New → Upload Theme**
2. Select `pressgrid-v2.5.0.zip` → **Install Now** → **Activate**
3. Configure at `Appearance → Customize`, `Layout Builder`, `Theme Ads`

### Option 2 — WP-CLI

```bash
wp theme install pressgrid-v2.5.0.zip --activate
```

### Option 3 — FTP / Manual

```bash
# Unzip and upload to your WordPress themes directory:
/wp-content/themes/pressgrid/
```

---

## ⚙️ Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| WordPress | 6.3 | 6.7+ |
| PHP | 8.0 | 8.2+ |
| MySQL / MariaDB | 5.7 / 10.3 | 8.0 / 10.6+ |
| Browser | ES2017+, CSS Grid | — |

---

## 🎨 Customizer

Navigate to **Appearance → Customize → PressGrid: Colors** to set your brand palette. All colors are output as CSS custom properties on `:root` with live postMessage preview.

| Setting | CSS Variable | Default |
|---|---|---|
| Primary Color | `--pg-primary` | `#1a73e8` |
| Secondary Color | `--pg-secondary` | `#0d47a1` |
| Accent Color | `--pg-accent` | `#e91e63` |
| Background Color | `--pg-bg` | `#ffffff` |
| Text Color | `--pg-text` | `#212121` |
| Link Hover Color | `--pg-link-hover` | `#0d47a1` |

---

## 🏗️ Layout Builder

**Appearance → Layout Builder** — Configure 8 homepage sections:

| Section | Default Layout | Description |
|---|---|---|
| `hero` | hero-grid | 1 large featured + sidebar cards |
| `latest_posts` | grid-3 | Most recent posts |
| `category_grid` | grid-4 | Posts from a category (cached) |
| `trending` | list | By comment count, last 7 days |
| `editor_picks` | grid-4 | Random featured posts (30-min cache) |
| `newsletter` | newsletter | Newsletter CTA section |
| `custom_html` | custom_html | Free-form HTML (wp_kses_post) |
| `ad_block` | ad_block | Renders between-posts ad zone |

> **Tip:** Assign a Business or Finance category to any section and the top bar Breaking News ticker will automatically switch to the Frankfurter forex ticker — no configuration needed.

---

## 📢 Advertisement System

**Appearance → Theme Ads** — Manage 6 zones:

| Zone | Location | Typical Size |
|---|---|---|
| `header` | Below sticky navigation | 728×90 |
| `sidebar_top` | Top of sidebar | 300×250 |
| `sidebar_middle` | Middle of sidebar | 300×600 |
| `in_article` | Above article content | Responsive |
| `between_posts` | Homepage ad block | Responsive |
| `footer` | Above site footer | 728×90 |

Each zone: enable/disable toggle, HTML/JS ad code input (sanitized with `wp_kses()`), desktop/mobile visibility, async load option.

---

## 🌤️ Weather Widget

Powered by **OpenWeatherMap** (free API key required).

**Appearance → Customize → PressGrid: Времето**

| Feature | Details |
|---|---|
| Current conditions | Temperature, feels like, hi/lo, humidity, wind, clouds |
| 5-day forecast | Daily icons + temps in a compact row |
| Top bar mini widget | `☀️ 7°C · Sofia` — hidden on small screens |
| Geolocation | ⊕ button — detects user location, no data stored |
| Cache | 30 min (current) · 1 hour (forecast) |
| Language | Bulgarian day names, translated UI |

👉 [How to get a free OpenWeatherMap API key →](API-GUIDE.md#2-openweathermap-api)

---

## 💱 Forex Ticker

Powered by **Frankfurter** — free, no API key, ECB data.

The forex ticker **automatically replaces** the Breaking News bar when the Layout Builder has an active section targeting a Business or Finance category. No configuration required.

**Appearance → Customize → PressGrid: Валути (Forex)**

| Setting | Default |
|---|---|
| Base currency | `EUR` |
| Target currencies | `USD, GBP, BGN, CHF, JPY` |
| Business category slug | Auto-detected (`business`, `biznes`, `бизнес`, `finance`, `финанси`…) |
| Force always on | `false` |

| Feature | Details |
|---|---|
| Data source | European Central Bank via frankfurter.app |
| Update frequency | Once daily (weekdays ~16:00 CET) |
| Cache | 6 hours |
| API key required | **No** |

👉 [Frankfurter API docs →](https://www.frankfurter.app/docs)

---

## 📖 Single Post Features (v2.5)

| Feature | How it works |
|---|---|
| **Reading progress bar** | Thin red line at top of viewport, JS-driven, single posts only |
| **Estimated read time** | `str_word_count / 200` — shown in post meta |
| **Share buttons** | Web Share API (mobile) + copy-to-clipboard fallback + LinkedIn — zero external JS |
| **Related posts** | 3 posts from same category, random order, no plugin |
| **Back-to-top button** | Appears after 400px scroll, smooth scroll, pure CSS + ~8 lines JS |

---

## ⚡ Performance Architecture

- **Transient caching** — hero, trending, category grid, editor picks (5–30 min TTL, cleared on `save_post`)
- **`no_found_rows: true`** on cached queries — skips `SQL_CALC_FOUND_ROWS`
- **Post ID caching** — only IDs stored in transients; query reconstructed from cache
- **Preconnect hints** — `<link rel="preconnect">` for Google Fonts, OpenWeatherMap, Frankfurter (conditional)
- **Deferred JS** — `<script defer>` strategy for all theme scripts
- **Native lazy loading** — `loading="lazy"` + `decoding="async"` on all non-LCP images
- **LCP optimization** — `fetchpriority="high"` on hero image
- **No render-blocking resources** — all scripts in footer or deferred
- **No jQuery, no Bootstrap** — 100% vanilla JavaScript
- **CSS Grid + Flexbox** — no CSS framework overhead (~22KB unminified CSS)
- **Redis/Memcached compatible** — `set_transient()` uses object cache backend when available

---

## 🔒 Security Hardening

- ✅ Every `$_POST` / `$_GET` — `wp_verify_nonce()` + `current_user_can()`
- ✅ All output — `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`
- ✅ Ad HTML — `wp_kses()` with strict allowed-tags whitelist
- ✅ Font upload — WOFF2 magic-byte validation (`0x774F4632`), 1MB cap, random rename, `.htaccess` protected directory
- ✅ Security headers via `wp_headers` filter — `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`
- ✅ WordPress version removed from `<head>` and all feeds
- ✅ Author enumeration blocked via `/?author=N` redirect
- ✅ Login errors genericized — no username enumeration
- ✅ No `eval()`, no direct DB queries, no obfuscated code
- ✅ Direct file access blocked in every PHP file
- ✅ Optional XML-RPC disable — `Appearance → Theme Security`
- ✅ All functions prefixed `pressgrid_` — no global namespace pollution
- ✅ All external API calls server-side only (`wp_remote_get`) — browser never contacts APIs directly

---

## 🌐 SEO & Schema

- **NewsArticle JSON-LD** on all single posts (auto-generated)
- **Open Graph** — `og:title`, `og:description`, `og:image`, `og:type`, `og:site_name`
- **Twitter Cards** — `summary_large_image` with featured image auto-population
- **Canonical `<link>`** on every page
- **Breadcrumb navigation** — semantic, accessible
- **Semantic HTML5** — `<header>`, `<main>`, `<article>`, `<nav>`, `<aside>`, `<footer>`
- **Reading time** estimation displayed in post meta
- **Pagination** `rel="prev"` / `rel="next"` via WordPress core

---

## 🌍 Translation

Text domain: **`pressgrid`** — `.pot` file included in `/languages/` (182 strings).

Bundled translations:

| Locale | File | Status |
|---|---|---|
| `bg_BG` | `pressgrid-bg_BG.po/mo` | Complete |
| `en_GB` | `pressgrid-en_GB.po/mo` | Complete |

```bash
# Generate POT file
wp i18n make-pot . languages/pressgrid.pot --domain=pressgrid

# Compile PO to MO
msgfmt languages/pressgrid-bg_BG.po -o languages/pressgrid-bg_BG.mo
```

Compatible with **Loco Translate** and **WPML**.

---

## 🔑 API Integrations

All external API calls are made **server-side** (PHP). The visitor's browser never contacts these services directly.

| API | Key required | Cost | Cache | Used for |
|---|---|---|---|---|
| [Google Fonts](https://fonts.google.com) | No | Free | Browser | Playfair Display, Barlow fonts |
| [OpenWeatherMap](https://openweathermap.org/api) | **Yes** (free tier) | $0 | 30 min / 1 hr | Weather widget |
| [Frankfurter](https://www.frankfurter.app) | No | Free | 6 hours | Forex ticker (ECB data) |

👉 **[Full API setup guide with step-by-step instructions →](API-GUIDE.md)**

---

## 🔧 Public Functions (Developer Reference)

```php
// Output breadcrumbs
pressgrid_breadcrumbs();

// Output post meta (author, date, reading time)
pressgrid_post_meta( $post_id );

// Render an ad zone
pressgrid_render_ad( 'sidebar_top' );

// Get first category label HTML
pressgrid_get_category_label( $post_id );

// Get cached hero WP_Query
$query = pressgrid_get_hero_posts( 3 );

// Get cached trending WP_Query
$query = pressgrid_get_trending_posts( 6 );

// Get cached category WP_Query
$query = pressgrid_get_category_posts( $cat_id, 4 );

// Check if a Business/Finance section is active (for forex ticker logic)
$show_forex = pressgrid_has_business_section();

// Get exchange rates from Frankfurter ECB API
$rates = pressgrid_get_forex_rates(); // ['base' => 'EUR', 'rates' => [...], 'date' => '...']

// Get weather data from OpenWeatherMap
$weather  = pressgrid_get_weather();   // current conditions array
$forecast = pressgrid_get_forecast();  // 5-day forecast array

// Get estimated reading time in minutes
$mins = pressgrid_reading_time( $post_id );
```

---

## ♿ Accessibility

- Skip-to-content link (`.skip-link`)
- All navigation has `aria-label`
- Mobile toggle with `aria-expanded` state management
- Keyboard-accessible dropdown menus with `aria-haspopup`
- `focus-visible` outline styles for keyboard users
- Sufficient color contrast ratios (WCAG AA)
- Semantic heading hierarchy on all templates
- Images require `alt` text — enforced via template functions
- Progress bar marked `aria-hidden="true"` (decorative)
- Share buttons with descriptive `aria-label` attributes

---

## 📋 WordPress.org Compliance

- ✅ GPL-2.0-or-later license
- ✅ Proper theme header in `style.css`
- ✅ Text domain: `pressgrid`
- ✅ All strings translatable
- ✅ No upsells, no tracking, no affiliate links
- ✅ No obfuscated code
- ✅ No required plugins
- ✅ No admin nags
- ✅ No hidden backlinks
- ✅ Developer credit is removable
- ✅ `readme.txt` included
- ✅ `screenshot.png` included (880×660)

---

## 📋 Changelog

### v2.5.0
- **New:** Frankfurter ECB forex ticker — auto-activates when Layout Builder has a Business/Finance section
- **New:** OpenWeatherMap weather widget (sidebar + top bar mini widget, 5-day forecast, geolocation)
- **New:** Reading progress bar on single posts
- **New:** Estimated read time in post meta
- **New:** Native share buttons (Web Share API + copy-to-clipboard + LinkedIn, zero external JS)
- **New:** Related posts section after article content (same category, no plugin)
- **New:** Back-to-top button (appears at 400px scroll)
- **New:** Sticky sidebar
- **New:** Card hover animations (`translateY` + shadow)
- **New:** Open Graph + Twitter Card meta tags
- **New:** NewsArticle JSON-LD schema
- **New:** Breadcrumb navigation
- **New:** `preconnect` hints for Google Fonts, OpenWeatherMap, Frankfurter
- **New:** `API-GUIDE.md` — step-by-step API setup documentation
- **New:** `bg_BG` and `en_GB` translations (182 strings)
- **Improved:** Hero grid layout rewrite (flex, 3-item limit, category labels, thumb hover)
- **Improved:** `pressgrid_post_meta()` now includes read time and modified date

### v1.0.0
- Initial release

---

## 📄 License

PressGrid WordPress Theme  
Copyright (C) 2024 [Milen Stanchev](https://stanchev.bg/)

Released under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## 👤 Author

**Milen Stanchev**  
🌐 [https://stanchev.bg/](https://stanchev.bg/)  
🐙 [github.com/stantchev](https://github.com/stantchev)

---

*PressGrid is an independent open-source project. No affiliation with WordPress.org or Automattic.*
