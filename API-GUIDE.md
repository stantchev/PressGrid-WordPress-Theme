# PressGrid — Ръководство за API интеграции

Темата използва **три external API-та**. Всички са безопасни за GDPR
(данните се кешират на сървъра, браузърът на читателя не прави директни заявки),
и всички данни минават само през WordPress transient cache.

---

## 1. Google Fonts API
**Използва се за:** Зареждане на шрифтовете Playfair Display, Barlow Condensed и Barlow.

### Нужен ли е API ключ?
**Не.** Google Fonts работи без регистрация и без ключ.

### Как работи в темата?
Шрифтовете се зареждат чрез `@import` в `style.css`:
```css
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display...');
```

### Алтернатива (privacy-first)
Ако искате да избегнете Google и да хоствате шрифтовете локално:
1. Отидете на [google-webfonts-helper.herokuapp.com](https://google-webfonts-helper.herokuapp.com)
2. Изтеглете `.woff2` файловете
3. Качете ги чрез **Appearance → Theme Settings → Font Upload**
4. Задайте ги в **Appearance → Customize → PressGrid: Typography**

### Документация
- https://developers.google.com/fonts/docs/getting_started

---

## 2. OpenWeatherMap API
**Използва се за:** Weather widget в sidebar-а и мини widget в top bar-а.

### Нужен ли е API ключ?
**Да — безплатен.** Free tier е напълно достатъчен.

### Как да получите API ключ (стъпка по стъпка)

1. Отидете на [openweathermap.org](https://openweathermap.org)
2. Кликнете **Sign In → Create an Account**
3. Попълнете имейл и парола, потвърдете имейла
4. След login отидете на **API Keys** (горе вдясно → вашето име → My API Keys)
5. Има автоматично генериран ключ на името **Default** — копирайте го
6. Или кликнете **Generate** за нов ключ с произволно име

> ⚠️ Новите ключове активират се след **10-15 минути**. Ако получите грешка веднага след регистрация — изчакайте малко.

### Конфигурация в темата
**Appearance → Customize → PressGrid: Времето**
- **OpenWeather API ключ** — поставете ключа от стъпка 5/6
- **Град** — формат `Sofia,BG` или `London,GB` или `New York,US`
- **Единици** — `metric` (°C) или `imperial` (°F)
- **Покажи прогноза за времето** — включва sidebar widget
- **Покажи в горната лента** — включва мини widget в top bar

### Безплатен план — лимити
| | Free |
|---|---|
| Заявки / ден | 1,000 |
| Заявки / мин | 60 |
| Прогноза | 5 дни |
| Цена | $0 |

Темата кешира данните **30 минути** (current) и **1 час** (forecast),
така че реалното потребление е ~48 заявки/ден — далеч под лимита.

### Endpoint-и, използвани от темата
```
GET https://api.openweathermap.org/data/2.5/weather
    ?q=Sofia,BG&appid=YOUR_KEY&units=metric&lang=bg

GET https://api.openweathermap.org/data/2.5/forecast
    ?q=Sofia,BG&appid=YOUR_KEY&units=metric&cnt=40&lang=bg
```

### Документация
- https://openweathermap.org/api
- https://openweathermap.org/current
- https://openweathermap.org/forecast5

---

## 3. Frankfurter API (валутни курсове)
**Използва се за:** Forex тикер в top bar-а — замества Breaking News тикера
автоматично когато сайтът има активна "Бизнес" / "Финанси" секция в Layout Builder-а.

### Нужен ли е API ключ?
**Не.** Frankfurter е напълно безплатен, open-source и без регистрация.
Данните идват директно от **Европейската централна банка (ЕЦБ)**.

### Как работи автоматичното включване
Темата проверява Layout Builder секциите:
- Ако някоя активна секция е насочена към категория с slug
  `business`, `biznes`, `бизнес`, `finance`, `финанси`, `economy` (и др.)
  → форекс тикерът се показва **автоматично** вместо Breaking News
- Ако искате да го включите ръчно без такава категория:
  **Customize → PressGrid: Валути → Показвай винаги** ✓

### Конфигурация в темата
**Appearance → Customize → PressGrid: Валути (Forex)**
- **Базова валута** — от коя валута да се изчисляват курсовете (по подразбиране: `EUR`)
- **Показвани валути** — разделени със запетая (по подразбиране: `USD,GBP,BGN,CHF,JPY`)
- **Slug на бизнес категорията** — само ако slug-ът е нестандартен (напр. `news-biz`)
- **Показвай винаги** — принудително включване без да проверява Layout Builder

### Поддържани валути (ISO 4217)
EUR, USD, GBP, BGN, CHF, JPY, CAD, AUD, CNY, RUB, TRY, RON, HUF, CZK, PLN
и още ~30+ валути. Пълен списък: https://api.frankfurter.app/currencies

### Endpoint, използван от темата
```
GET https://api.frankfurter.app/latest?from=EUR&to=USD,GBP,BGN,CHF,JPY
```

### Примерен отговор
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

### Ограничения
- Обновява се **веднъж дневно** (работни дни, ~16:00 CET)
- Без rate limit за нормална употреба
- Уикенди и празници — показват се последните налични данни

Темата кешира данните **6 часа**, така че практически правите ~4 заявки/ден.

### Документация
- https://www.frankfurter.app/docs
- GitHub: https://github.com/hakanensari/frankfurter

---

## Обобщение

| API | Ключ | Цена | Cache в темата |
|---|---|---|---|
| Google Fonts | Не | Безплатен | Браузърен cache |
| OpenWeatherMap | Да (безплатен) | Free tier: $0 | 30 мин / 1 час |
| Frankfurter (ЕЦБ) | Не | Безплатен | 6 часа |

---

## GDPR бележка

Всички API заявки се правят **от сървъра** (PHP `wp_remote_get`),
не от браузъра на посетителя. Данните се съхраняват временно като
WordPress transients в базата данни.

Единственото изключение е **Google Fonts** — браузърът зарежда шрифтовете
директно от Google. Ако това е проблем, използвайте локалния upload (виж т.1).

За OpenWeatherMap геолокацията (бутонът ⊕ в weather widget-а) използва
Browser Geolocation API — браузърът пита потребителя за разрешение преди
да изпрати координатите. Не се съхраняват данни.
