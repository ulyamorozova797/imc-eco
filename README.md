# IMC Montan — сайт

Многостраничный сайт экологического консалтинга для горнодобывающей отрасли.
PHP 8.1+ без фреймворков и сборщиков, ES-модули, всё локально в `assets/`.

---

## 1. Требования к хостингу

- PHP **7.4–8.5**, рекомендуется 8.2/8.3 (проверено на 8.3), расширения: `mbstring`, `json`, `gd` или `fileinfo` (для `getimagesize`), `openssl` (SMTP/SSL).
- Apache с `mod_rewrite` (ЧПУ), желательно `mod_headers`, `mod_expires`, `mod_deflate`/`mod_brotli`.
  На nginx перенесите правила из `.htaccess` (см. раздел 10).
- Права на запись для PHP в `assets/data/` — туда автоматически пересобирается `search-index.json`.
- HTTPS. Хостинг на возобновляемой энергии — по выбору заказчика (подпись в футере правится в `assets/include/footer.php`).

## 2. Локальный запуск

**Встроенный сервер PHP (быстрее всего):**

```bash
cd путь/к/сайту
php -S localhost:8000 router.php
# открыть http://localhost:8000
```

`router.php` повторяет правила `.htaccess` (ЧПУ, 404) и нужен только для `php -S`.

**OSPanel / XAMPP / MAMP:** скопируйте папку в `domains/` (OSPanel) или `htdocs/` (XAMPP), откройте домен/папку в браузере.
Сайт работает и в корне домена, и в подпапке: `BASE_URL` определяется автоматически (`assets/include/config.php`).
Если сайт лежит в подпапке — поправьте путь `ErrorDocument 404` в `.htaccess`.

## 2.1. Размещение на хостинге (на примере Beget)

1. **Версия PHP.** Панель → «Сайты» → у нужного домена кнопка настроек PHP → выберите **8.2 или 8.3** → «Применить».
2. **Файлы.** Загрузите *содержимое* архива (не папку целиком) в `ваш-домен/public_html/` — так, чтобы
   `public_html/index.php`, `public_html/.htaccess` и `public_html/assets/` лежали рядом.
   Удобнее всего: загрузить zip через файловый менеджер и распаковать там же. Проверьте, что скрытый `.htaccess` на месте.
   Если в `public_html` был стандартный `index.html` хостинга — удалите его.
3. **Проверка.** Откройте `https://ваш-домен/check.php` — страница покажет версию PHP, расширения, права
   и первую ошибку, если главная не собирается. **После проверки удалите `check.php`.**
4. Если check.php говорит «главная собирается без ошибок», а сайт отдаёт 500 — закомментируйте в `.htaccess`
   строку `Options -Indexes` (некоторые хостинги её запрещают).
5. Права: папки 755, файлы 644; `assets/data/` должна быть доступна PHP для записи.

## 3. Структура

```
/
├── index.php                 главная (точка входа)
├── .htaccess                 ЧПУ, HTTPS, кэш, сжатие, 404
├── router.php                роутер для php -S (на хостинге не нужен)
├── favicon.ico, robots.txt, sitemap.xml, sitemap.php, rss.php (/rss.xml)
├── about/  services/{ecological-audit,esia,environmental-support}/
├── news/  news/item/         /news/<slug>/
├── articles/  articles/item/ /articles/<slug>/
├── opinion/  opinion/item/   /opinion/<slug>/  (тот же шаблон, type = opinion)
├── podcasts/  podcasts/item/ /podcasts/<slug>/
├── cases/item/               /cases/<slug>/
├── contacts/ feedback/ privacy/ success/ 404/ ui-kit/
└── assets/
    ├── css/        tokens.css · base.css · components.css · pages/{home,inner}.css
    ├── js/         main.js + modules/ (slider, hero, topo, map, form, palette,
    │               lightbox, process, counter, tilt, ba, tabs, video, toc, util)
    ├── include/    config.php, head.php, header.php, footer.php, form.php, breadcrumbs.php,
    │               cookie-banner.php, utm.php, team-card.php, article-card.php, podcast-card.php,
    │               card-service.php (карточки, page_hero, cta_band), video-embed.php, toc.php,
    │               service-page.php, feed.php, article.php (общие шаблоны)
    ├── data/       services.php, team.php, cases.php, news.php, articles.php (+ articles/*.html),
    │               opinion.php, podcasts.php, sliders.php, projects.json,
    │               search-index.json (генерируется), i18n/{ru,en}.php
    ├── img/        logo/ team/ hero/ services/ cases/ gallery/ certificates/ news/ articles/
    │               opinion/ podcasts/ partners/ og/ topo/ icons/sprite.svg, brand-sprite.svg
    ├── fonts/      Literata, Onest, JetBrains Mono (woff2, latin + cyrillic)
    ├── html/       inline-SVG эмблемы для лоадера, шаблон письма email-lead.html
    ├── libs/       gsap (+ScrollTrigger), maplibre, glightbox
    └── php/        send.php, search-index.php, PHPMailer/
```

Каждая страница задаёт `$title`, `$description`, `$ogImage`, `$bodyClass` и подключает
`require_once __DIR__ . '/../assets/include/config.php';`. Активный пункт меню определяется по URL.
CSS/JS подключаются через `asset()` с `?v=filemtime` — кэш сбрасывается сам при изменении файла.

## 4. Как заменить демо-фото

Все фото объектов — **демо-иллюстрации** в фирменной гамме. Чтобы заменить, положите свой файл
**с тем же именем** в ту же папку. Подписи и alt — в `assets/data/sliders.php`.

| Тип | Папка | Пропорции | Рекомендуемый размер |
|---|---|---|---|
| Hero-слайдер | `img/hero/hero-01…05` | 16:9 | 2400×1350 |
| Услуги, этапы «Как мы работаем» | `img/services/` | 4:3 | 1600×1200 |
| Кейсы «до/после» | `img/cases/case-NN-before/after` | 16:10 | 1600×1000 |
| Галерея «Мы в полях» | `img/gallery/gallery-NN` | 3:2 | 1600×1067 |
| Статьи, мнения | `img/articles/`, `img/opinion/` | 16:9 | 1600×900 |
| Новости | `img/news/` | 4:3 | 960×720 |
| Обложки подкастов | `img/podcasts/` | 16:9 | 1280×720 |
| Сертификаты | `img/certificates/` | A4 (1:1,414) | 1200×1697 |

**Форматы и srcset.** Функция `img()` сама собирает `srcset` из файлов вида `имя-480.webp`, `имя-960.webp`,
`имя-1600.webp`, `имя-2400.webp` (+ такие же `.avif`). Достаточно и одного файла `имя.webp` (или `.jpg`) —
он будет показан, просто без адаптивных размеров. Вёрстка держит пропорции через `aspect-ratio` +
`object-fit: cover`, так что фото другого размера ничего не сломает.

Нарезать размеры можно одной командой (ImageMagick):

```bash
for w in 480 960 1600 2400; do
  magick hero-01.jpg -resize ${w}x -quality 74 hero-01-$w.webp
  magick hero-01.jpg -resize ${w}x -quality 50 hero-01-$w.avif
done
cp hero-01-1600.webp hero-01.webp
```

Ни одно изображение не должно весить больше **300 КБ** — проверьте после экспорта.

**Добавить фото в слайдер/галерею:** положите файл и допишите строку в нужный массив `assets/data/sliders.php`:

```php
['file' => 'gallery/gallery-11', 'alt' => 'Что на фото', 'caption' => 'Подпись · место', 'src' => 'источник'],
```

## 5. Команда

Данные — `assets/data/team.php`. Фото — `assets/img/team/<slug>.webp` (960×1200) и `<slug>-480.webp` (+ `.avif`),
формат **4:5**, глаза на ~38% высоты кадра. Фото четырёх сотрудников уже обработаны так (одинаковые
кадрирование и тон, вес 10–100 КБ).

**Как добавить фото пятому сотруднику:** экспортируйте два размера (`ivanov.webp` 960×1200 и `ivanov-480.webp`),
положите в `assets/img/team/`, затем в записи сотрудника добавьте `'photo' => 'team/ivanov'`.
Пока поля `photo` нет (или файла нет) — показывается фирменная заглушка с инициалами.
Если кадр отличается — поправьте `'pos' => '50% 30%'` (object-position).

**Новый сотрудник:** скопируйте любой блок массива и заполните `slug, name, role, spec, creds, years, bio, facts`.
Для ФИО/должностей помечены места `[…]` — замените демо-значения реальными.

## 6. Контент

**Услуга.** Запись в `assets/data/services.php` (схема: `task` → `do` → `result` → `timeline`)
+ папка `services/<slug>/index.php` — скопируйте любую из трёх и поменяйте `$serviceSlug`.

**Новость.** Запись в `assets/data/news.php` (`slug, date, title, image, excerpt, body`). Картинка — `img/news/`.

**Статья / «Мнение на тему».** Одна модель — `assets/data/articles.php`:
```php
['slug' => 'my-article', 'type' => 'article', // или 'opinion'
 'title' => '…', 'excerpt' => '…', 'date' => '2026-10-01',
 'author' => 'sokolova', // slug из team.php
 'cover' => 'articles/my-article', 'tags' => ['аудит', 'нормативка']],
```
Текст — отдельный файл `assets/data/articles/<slug>.html` (или `.md` — есть простой парсер).
Поддерживается: `h2/h3` (из них строится оглавление), списки, `blockquote` + `cite`, `table`, `figure`,
`<div class="callout" role="note">`, сноски `<sup class="fn">`. Видео VK в тексте — отдельной строкой:
`[[vk https://vkvideo.ru/video-123456_456239017]]`. Картинка из assets: `{{img:gallery/gallery-07-960.webp}}`.
Время чтения считается автоматически. Теги для фильтра — список `$tags` в `assets/include/feed.php`.

**Кейс.** Запись в `assets/data/cases.php` + фото `img/cases/case-NN-before/after`. Точка на карте — `assets/data/projects.json`
(`coords: [долгота, широта]`, `type: audit | esia | support | reclamation`, `case: slug` для ссылки на страницу кейса).

**Подкаст.** Запись в `assets/data/podcasts.php`:
```php
['slug' => 'ep-05-water', 'number' => 5, 'title' => '…', 'description' => '…',
 'date' => '2026-10-20', 'duration' => '45:10',
 'vk_url' => 'https://vkvideo.ru/video-123456_456239017', // обычная ссылка из браузера
 'cover' => 'podcasts/ep-05-water',                       // 1280×720, можно оставить '' — будет фирменная заглушка
 'tags' => ['аудит'],
 'guests' => [['name' => 'Соколова', 'role' => '…', 'team' => 'sokolova'], ['name' => 'Гость', 'role' => '…']],
 'timestamps' => ['00:00' => 'Вступление', '12:30' => 'Тема выпуска']],
```
`vk_embed()` сам разбирает ссылку (`vkvideo.ru/video-…`, `vk.com/video-…`) и строит плеер
`https://vk.com/video_ext.php?oid=…&id=…&hd=2`. Плеер грузится только по клику; таймкоды открывают видео с нужной секунды.
⚠️ Сейчас в данных стоят **демо-ссылки** `video-000000_…` — замените на реальные выпуски и ссылку на канал в `config.php`.

**Поиск Ctrl/⌘+K** индексирует услуги, кейсы, статьи, мнения, подкасты, новости и команду.
Индекс `assets/data/search-index.json` пересобирается сам, когда меняется любой файл данных
(вручную: `php assets/php/search-index.php`).

**Sitemap.** После добавления материалов: `php sitemap.php https://ваш-домен.ru > sitemap.xml`
(или отдавайте `/sitemap.php` напрямую). RSS — `/rss.xml`, собирается на лету.

## 7. Почта для заявок

`assets/include/config.php` → константа `MAIL`:

```php
const MAIL = [
  'to' => 'info@imc-montan.ru',          // куда приходят заявки (можно несколько через запятую)
  'from' => 'noreply@imc-montan.ru',
  'smtp' => true,                         // false — через mail() хостинга
  'host' => 'smtp.yandex.ru', 'port' => 465, 'secure' => 'ssl',
  'user' => 'noreply@imc-montan.ru', 'pass' => 'пароль приложения',
];
```

Отправка — `assets/php/send.php` (PHPMailer 6.9). В письмо попадают поля формы,
страница, UTM-метки (сохраняются в cookie на 30 дней) и IP. Шаблон письма — `assets/html/email-lead.html`.

- **Telegram:** заполните `TELEGRAM_BOT` (`token` от @BotFather, `chat_id` чата/канала).
- **Yandex SmartCaptcha:** ключи в `CAPTCHA` (`client`, `server`); пустые — капча выключена, работает honeypot + проверка времени + лимит 5 заявок / 10 минут с IP.
- **Яндекс.Метрика:** `METRIKA_ID`. Цели: `form_submit`, `form_step_1…3`, `cta_hero`, `cta_header`, `cta_footer`, `cta_band`.
- **Вебмастер:** `YANDEX_VERIFICATION`.

Реквизиты, телефон, адрес, цифры («лет на рынке», «проектов», «регионов») — массив `SITE` там же.

## 8. Дизайн-система

Страница `/ui-kit/` (закрыта от индексации): логотип во всех версиях, токены, типографика, кнопки, бейджи,
карточки, карточка сотрудника и заглушка, формы, слайдеры.

- Токены — `assets/css/tokens.css` (светлая и тёмная тема, `prefers-color-scheme` + переключатель с `localStorage`).
- Логотип: `img/logo/logo.svg` (оригинал), `logo-white.svg`, `emblem.svg`, `emblem-white.svg`, `favicon.svg/.ico`,
  `apple-touch-icon.png`. В шапке — через `<use>` из `img/brand-sprite.svg` с `currentColor`.
- Иконки — Lucide (ISC), спрайт `img/icons/sprite.svg`, вызов `icon('arrow-right')`.

## 9. Производительность и доступность

- Первая загрузка главной ≈ 0,7 МБ (HTML ≈ 35 КБ в gzip); фото, карта, Canvas-изолинии, GSAP и лайтбокс
  грузятся лениво при подходе к зоне видимости. На слабых устройствах (`saveData`, ≤ 2 ядра/2 ГБ) — статичные SVG.
- Карта: MapLibre GL + бесплатные тайлы OpenFreeMap (без ключа). Если WebGL или тайлы недоступны —
  работает статичная схема с теми же точками и карточками.
- `prefers-reduced-motion` отключает автопрокрутку, Ken Burns, изолинии, параллакс и переходы.
- Клавиатура: все слайдеры (стрелки, Home/End), «до/после» (range), табы, палитра поиска, модальные окна.
- Переходы между страницами — `@view-transition` (cross-document), фолбэк — плавное затемнение.
- Lenis: положите `lenis.min.js` в `assets/libs/lenis/` — плавный скролл включится автоматически
  (в поставку не вошёл: без него используется нативный `scroll-behavior: smooth`).
  Swiper не используется: слайдеры написаны на нативном scroll-snap (свайп, drag, клавиатура, aria), это легче на ~150 КБ.

## 10. nginx (если не Apache)

```nginx
location ~ ^/assets/(include|data|html)/.+\.php$ { deny all; }
location = /rss.xml { rewrite ^ /rss.php last; }
location ~ ^/(news|articles|opinion|podcasts|cases)/([a-z0-9-]+)/?$ { try_files $uri $uri/ /$1/item/index.php?slug=$2&$args; }
location / { try_files $uri $uri/ /404/index.php; }
error_page 404 /404/index.php;
```

## 11. Что заменить перед запуском

- [ ] Имена, должности и аккредитации команды (`team.php`), ФИО и фото пятого сотрудника
- [ ] Цифры, реквизиты, адрес, координаты офиса, соцсети (`config.php` → `SITE`)
- [ ] Почта / SMTP / Telegram / капча / Метрика (`config.php`)
- [ ] Демо-фото объектов, сертификаты, логотипы клиентов (`img/partners/*.svg`, `sliders.php`)
- [ ] Ссылки на выпуски VK и на канал (`podcasts.php`, `SITE['vk_video']`)
- [ ] Текст политики ПДн — проверка юристом (`privacy/index.php`)
- [ ] Домен в `robots.txt`, пересобрать `sitemap.xml`, включить редирект на HTTPS в `.htaccess`
