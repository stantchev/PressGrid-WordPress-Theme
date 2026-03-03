=== PressGrid ===
Contributors: milenstanchev
Tags: news, magazine, grid-layout, custom-colors, custom-logo, custom-menu, featured-images, footer-widgets, full-width-template, post-formats, rtl-language-support, sticky-post, theme-options, threaded-comments, translation-ready, two-columns, wide-blocks
Requires at least: 6.3
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

PressGrid is a high-performance WordPress theme for news and magazine websites.

== Description ==

PressGrid is a production-ready, security-hardened WordPress theme optimized for high-traffic news and magazine websites. It features a powerful Layout Builder, fully configurable Advertisement Zones, Customizer-driven CSS variable theming, and Lighthouse 95+ performance targeting.

Features include:

* Layout Builder (Appearance → Layout Builder) with drag-and-drop section ordering
* 8 homepage sections: Hero, Latest Posts, Category Grid, Trending, Editor Picks, Newsletter, Custom HTML, Ad Block
* 6 Ad Zones with enable/disable, mobile/desktop visibility toggles, and async loading
* Full Customizer integration with CSS custom property theming
* Secure local .woff2 font upload system with MIME validation
* Schema.org (NewsArticle), Open Graph, Twitter Cards, Breadcrumbs
* Breaking news ticker
* No jQuery, no Bootstrap — vanilla JS only
* Transient caching for expensive queries
* Lazy loading with native loading="lazy" and IntersectionObserver
* Fully translatable (text domain: pressgrid)
* WCAG 2.1 AA accessibility
* XML-RPC toggle, security headers, login error obfuscation

== Installation ==

1. In your WordPress admin, go to Appearance → Themes → Add New
2. Click "Upload Theme" and select the pressgrid.zip file
3. Click "Install Now" and then "Activate"
4. Go to Appearance → Customize to set colors and typography
5. Go to Appearance → Layout Builder to configure homepage sections
6. Go to Appearance → Theme Ads to configure advertisement zones
7. Configure widgets in Appearance → Widgets

== Frequently Asked Questions ==

= How do I configure the homepage layout? =
Go to Appearance → Layout Builder. Enable/disable sections, choose layouts, select categories, and set post counts.

= How do I add ads? =
Go to Appearance → Theme Ads. Paste your ad code (AdSense, custom HTML, etc.) into the appropriate zone.

= How do I upload a custom font? =
Go to Appearance → Font Upload. Upload a .woff2 file (max 1MB). Then go to Appearance → Customize → Typography to set the font family name and apply it to elements.

= Can I remove the developer credit? =
Yes. Go to Appearance → Customize → PressGrid: Layout → uncheck "Show Developer Credit in Footer".

= Is this theme compatible with caching plugins? =
Yes. PressGrid is fully compatible with WP Super Cache, W3 Total Cache, LiteSpeed Cache, and Redis Object Cache.

= How do I disable XML-RPC? =
Go to Appearance → Theme Security and check "Disable XML-RPC endpoint".

== Changelog ==

= 1.0.0 =
* Initial release

== Credits ==

PressGrid was developed by Milen Stanchev (https://stanchev.bg/).

This theme bundles no third-party libraries, fonts, or scripts. All code is original.

== License ==

PressGrid WordPress Theme, Copyright (C) 2024 Milen Stanchev
PressGrid is distributed under the terms of the GNU GPL v2 or later.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
