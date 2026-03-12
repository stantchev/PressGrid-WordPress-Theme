# PressGrid — API Integration Guide

The theme uses **three external APIs**. All are GDPR-safe — data is cached on the server and the visitor's browser never makes direct requests to any of these services. All data flows exclusively through WordPress transient cache.

---

## 1. Google Fonts API
**Used for:** Loading the Playfair Display, Barlow Condensed, and Barlow typefaces.

### Is an API key required?
**No.** Google Fonts works without registration or a key of any kind.

### How it works in the theme
Fonts are loaded via `@import` in `style.css`:
```css
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display...');
```

### Privacy-first alternative (self-hosted fonts)
If you want to avoid Google and host the fonts locally:
1. Go to [google-webfonts-helper.herokuapp.com](https://google-webfonts-helper.herokuapp.com)
2. Download the `.woff2` files
3. Upload them via **Appearance → Theme Settings → Font Upload**
4. Set the font family name in **Appearance → Customize → PressGrid: Typography**

### Documentation
- https://developers.google.com/fonts/docs/getting_started

---

## 2. OpenWeatherMap API
**Used for:** Weather widget in the sidebar and the mini widget in the top bar.

### Is an API key required?
**Yes — free.** The free tier is more than sufficient.

### How to get an API key (step by step)

1. Go to [openweathermap.org](https://openweathermap.org)
2. Click **Sign In → Create an Account**
3. Fill in your email and password, then confirm your email address
4. After logging in, go to **API Keys** (top right → your name → My API Keys)
5. There is an auto-generated key called **Default** — copy it
6. Or click **Generate** to create a new key with a custom name

> ⚠️ New keys take **10–15 minutes** to activate. If you get an error immediately after registration — wait a moment and try again.

### Configuration in the theme
**Appearance → Customize → PressGrid: Weather**
- **OpenWeather API key** — paste the key from step 5/6
- **City** — format: `Sofia,BG` or `London,GB` or `New York,US`
- **Units** — `metric` (°C) or `imperial` (°F)
- **Show weather forecast** — enables the full sidebar widget
- **Show in top bar** — enables the mini widget in the top bar

### Free plan limits
| | Free |
|---|---|
| Requests / day | 1,000 |
| Requests / min | 60 |
| Forecast range | 5 days |
| Price | $0 |

The theme caches data for **30 minutes** (current conditions) and **1 hour** (forecast), so real-world usage is approximately 48 requests/day — well within the free limit.

### Endpoints used by the theme
```
GET https://api.openweathermap.org/data/2.5/weather
    ?q=Sofia,BG&appid=YOUR_KEY&units=metric&lang=en

GET https://api.openweathermap.org/data/2.5/forecast
    ?q=Sofia,BG&appid=YOUR_KEY&units=metric&cnt=40&lang=en
```

### Documentation
- https://openweathermap.org/api
- https://openweathermap.org/current
- https://openweathermap.org/forecast5

---

## 3. Frankfurter API (Exchange Rates)
**Used for:** Forex ticker in the top bar — automatically replaces the Breaking News ticker when the site has an active "Business" or "Finance" section in the Layout Builder.

### Is an API key required?
**No.** Frankfurter is completely free, open-source, and requires no registration whatsoever. Data comes directly from the **European Central Bank (ECB)**.

### How the automatic activation works
The theme inspects the Layout Builder sections:
- If any active section targets a category with a slug of
  `business`, `biznes`, `finance`, `economy`, `markets`, `money` (and others)
  → the forex ticker is shown **automatically** in place of Breaking News
- To enable it manually without such a category:
  **Customize → PressGrid: Currencies → Always show** ✓

### Configuration in the theme
**Appearance → Customize → PressGrid: Currencies (Forex)**
- **Base currency** — the currency rates are calculated from (default: `EUR`)
- **Display currencies** — comma-separated (default: `USD,GBP,BGN,CHF,JPY`)
- **Business category slug** — only needed if your category has a non-standard slug (e.g. `news-biz`)
- **Always show** — forces the ticker on regardless of Layout Builder state

### Supported currencies (ISO 4217)
EUR, USD, GBP, BGN, CHF, JPY, CAD, AUD, CNY, RUB, TRY, RON, HUF, CZK, PLN
and 30+ more. Full list: https://api.frankfurter.app/currencies

### Endpoint used by the theme
```
GET https://api.frankfurter.app/latest?from=EUR&to=USD,GBP,BGN,CHF,JPY
```

### Example response
```json
{
  "amount": 1.0,
  "base": "EUR",
  "date": "2025-03-07",
  "rates": {
    "BGN": 1.9558,
    "CHF": 0.9305,
    "GBP": 0.8351,
    "JPY": 161.28,
    "USD": 1.0823
  }
}
```

### Limitations
- Updates **once per day** (weekdays, ~16:00 CET)
- No rate limit for normal usage
- Weekends and public holidays — the last available data is shown

The theme caches data for **6 hours**, so in practice you make ~4 requests/day.

### Documentation
- https://www.frankfurter.app/docs
- GitHub: https://github.com/hakanensari/frankfurter

---

## Summary

| API | Key required | Cost | Cache |
|---|---|---|---|
| Google Fonts | No | Free | Browser cache |
| OpenWeatherMap | Yes (free) | $0 | 30 min / 1 hour |
| Frankfurter (ECB) | No | Free | 6 hours |

---

## GDPR Note

All API requests are made **server-side** (PHP `wp_remote_get`) — the visitor's browser never contacts these services directly. Data is stored temporarily as WordPress transients in the database.

The one exception is **Google Fonts** — the browser loads the font files directly from Google's CDN. If this is a concern for your privacy policy, use the local font upload option instead (see section 1).

For the OpenWeatherMap geolocation feature (the ⊕ button in the weather widget), the browser uses the native Geolocation API and asks the user for permission before sending any coordinates. No location data is stored or transmitted beyond the single API request.
