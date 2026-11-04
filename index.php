<?php
$title = 'IMC Montan — экологическое и социальное сопровождение горных проектов';
$description = 'Экологическое и социальное сопровождение горного проекта на всех стадиях жизненного цикла: оценка экологических рисков в деньгах, ESIA по стандартам IFC и ЕБРР, рекультивация и ликвидация.';
$ogImage = 'og/default.jpg';
$bodyClass = 'page-home has-hero';
$pageCss = ['home'];
$showLoader = true;
require_once __DIR__ . '/assets/include/config.php';

$sl = data('sliders');
$services = data('services');
$team = data('team');
$news = data('news');
usort($news, fn($a, $b) => strcmp($b['date'], $a['date']));
$featured = array_values(array_filter($news, fn($n) => !empty($n['featured'])))[0] ?? null;
$restNews = array_values(array_filter($news, fn($n) => empty($n['featured'])));
$opinions = array_values(array_filter(data('articles'), fn($a) => $a['type'] === 'opinion'));
usort($opinions, fn($a, $b) => strcmp($b['date'], $a['date']));
$projects = json_decode((string) file_get_contents(DATA . 'projects.json'), true);
$hideFooterCta = true; // на главной есть свой блок «Обсудить проект»
$tc = ['audit' => '#045C94', 'esia' => '#3C8BC0', 'reclamation' => '#2E9474'];
$schema = array_merge(team_schema($team), array_map(fn($s) => ['@context' => 'https://schema.org', '@type' => 'Service', 'name' => $s['title'], 'description' => $s['lead'], 'provider' => ['@id' => SITE_ORIGIN . BASE_URL . '#org'], 'areaServed' => ['RU', 'KZ'], 'url' => abs_url('services/' . $s['slug'] . '/')], $services));

require INC . 'head.php';
require INC . 'header.php';
?>
<main id="main">

<section class="hero" data-module="hero" data-eager aria-roledescription="карусель" aria-label="Главный экран">
  <div class="hero__slides">
    <?php foreach ($sl['hero'] as $i => $h): ?>
    <div class="hero__slide<?= $i === 0 ? ' is-active' : '' ?>" data-caption="<?= e($h['caption']) ?>" aria-hidden="<?= $i ? 'true' : 'false' ?>">
      <?= img($h['file'], $h['alt'], ['sizes' => '100vw', 'w' => 2400, 'h' => 1350, 'lazy' => $i > 0]) ?>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="hero__topo" data-module="topo" data-eager data-seed="5" data-levels="16" data-cell="16" data-colors="#B5D96B,#7CC4A4" data-alpha=".5">
    <?= topo_svg('hero') ?>
  </div>
  <div class="hero__shade" aria-hidden="true"></div>

  <div class="container hero__content">
    <p class="eyebrow eyebrow--light reveal">Экологический консалтинг для горной отрасли</p>
    <h1 class="hero__title"><em>ЭкоЛогичные</em> решения в&nbsp;недропользовании</h1>
    <p class="hero__sub">Берём на себя экологическое и социальное сопровождение горного проекта на всех стадиях жизненного цикла предприятия.</p>
    <div class="hero__actions">
      <a class="btn btn--light magnetic" href="#contact" data-goal="cta_hero">Обсудить проект <?= icon('arrow-right') ?></a>
      <a class="btn btn--ghost-light" href="#services">Наши услуги</a>
    </div>
  </div>

  <div class="hero__altimeter altimeter" role="group" aria-label="Кадры">
    <span class="altimeter__unit map-meta">отм., м</span>
    <?php foreach ($sl['hero'] as $i => $h): ?>
    <button class="altimeter__tick" type="button" aria-label="Кадр <?= $i + 1 ?>: <?= e($h['caption']) ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>"><span><?= str_pad((string) $h['alt_m'], 3, '0', STR_PAD_LEFT) ?></span></button>
    <?php endforeach; ?>
  </div>

  <div class="hero__corner">
    <div class="map-meta hero__coords"><span data-hero-caption><?= e($sl['hero'][0]['caption']) ?></span></div>
    <div class="scalebar"><span class="scalebar__bar"></span><span class="scalebar__labels"><span>0</span><span>1 км</span></span></div>
  </div>
  <div class="hero__nav">
    <button class="icon-btn" type="button" data-hero-prev aria-label="Предыдущий кадр"><?= icon('chevron-left') ?></button>
    <button class="icon-btn" type="button" data-hero-next aria-label="Следующий кадр"><?= icon('chevron-right') ?></button>
  </div>
  <p class="sr-only" aria-live="polite" data-hero-live></p>
  <svg class="hero__wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 80V44c160-30 320-40 480-26s320 46 480 42 320-40 480-48v68Z"/></svg>
</section>

<section class="section section--tight home-news" id="news" aria-labelledby="news-title">
  <div class="container">
    <div class="marquee news-ticker" aria-label="Коротко о главном">
      <div class="marquee__track">
        <?php for ($k = 0; $k < 2; $k++): ?>
        <ul class="marquee__list" <?= $k ? 'aria-hidden="true"' : '' ?> role="list">
          <?php foreach ($news as $n): if (empty($n['ticker'])) continue; ?>
          <li class="marquee__item news-ticker__item"><a href="<?= url('news/' . $n['slug'] . '/') ?>"<?= $k ? ' tabindex="-1"' : '' ?>><span class="mono"><?= ru_date($n['date'], false) ?></span> <?= e($n['ticker']) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <?php endfor; ?>
      </div>
    </div>

    <div class="section-head section-head--split">
      <div><p class="eyebrow"><span class="num">01</span> Новости</p><h2 id="news-title">Что у нас <em>нового</em></h2></div>
      <p><a class="link-arrow" href="<?= url('news/') ?>">Все новости <?= icon('arrow-up-right') ?></a></p>
    </div>

    <div class="home-news__grid">
      <?php if ($featured) echo news_feature($featured); ?>
      <div class="slider home-news__slider" data-module="slider" aria-label="Последние новости">
        <div class="slider__track" style="--gap:20px">
          <?php foreach (array_slice($restNews, 0, 6) as $i => $n): ?><div class="slider__slide home-news__slide"><?= news_card($n, $i) ?></div><?php endforeach; ?>
        </div>
        <div class="slider__controls">
          <span class="slider__count" aria-hidden="true"></span>
          <button class="slider__btn" type="button" data-prev aria-label="Предыдущая новость"><?= icon('arrow-left') ?></button>
          <button class="slider__btn" type="button" data-next aria-label="Следующая новость"><?= icon('arrow-right') ?></button>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="trust" aria-label="Нам доверяют">
  <div class="container trust__inner">
    <p class="map-meta trust__label">Среди клиентов группы IMC Montan — горнодобывающие компании, холдинги, банки и финансовые организации</p>
    <div class="marquee" data-compass-zone>
      <div class="marquee__track">
        <?php for ($k = 0; $k < 2; $k++): ?>
        <ul class="marquee__list" <?= $k ? 'aria-hidden="true"' : '' ?> role="list">
          <?php foreach ($sl['partners'] as $p): ?>
          <li class="marquee__item trust__name"><?= e($p['name']) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--tight metrics" data-module="counter" aria-labelledby="metrics-title">
  <div class="container">
    <h2 class="sr-only" id="metrics-title">IMC Montan в цифрах</h2>
    <div class="metrics__grid">
      <?php $metrics = [
        [SITE['years'], '', plural(SITE['years'], 'год', 'года', 'лет'), 'опыта работы в России и СНГ — с 1992 года.'],
        [SITE['projects'], '+', 'проектов', 'реализовано по всему миру — от Scoping Study до сопровождения закрытия.'],
        [SITE['clients'], '+', 'клиентов', 'горнодобывающие компании и холдинги, банки, международные финансовые организации.'],
        [SITE['countries'], '', 'стран', 'компании группы: Россия, Великобритания, Сербия, Армения, Казахстан, Узбекистан.'],
      ];
      foreach ($metrics as $i => [$n, $suf, $unit, $txt]): ?>
      <div class="metric reveal" style="--d:<?= $i * .08 ?>s">
        <p class="metric__num"><span data-count="<?= $n ?>"><?= $n ?></span><?= $suf ?></p>
        <p class="metric__unit mono"><?= e($unit) ?></p>
        <p class="metric__text"><?= e($txt) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--alt" id="services" aria-labelledby="services-title">
  <?= wave('top', 'var(--bg-alt)') ?>
  <div class="container">
    <div class="section-head section-head--split">
      <div><p class="eyebrow"><span class="num">02</span> Услуги</p><h2 id="services-title">Экология и социальная сфера проекта — <em>от изысканий до закрытия</em></h2></div>
      <p>Пять направлений, одна команда. Оцениваем риски в деньгах, готовим ESIA для международных банков, сопровождаем предприятие и планируем закрытие с первого дня.</p>
    </div>
    <div class="bento" data-module="tilt">
      <?php foreach ($services as $i => $s) echo service_tile($s, 'bento__tile--' . ['a', 'b', 'c', 'd', 'e'][$i], $i); ?>
    </div>
  </div>
</section>

<section class="process" data-module="process" aria-labelledby="process-title">
  <div class="process__pin">
    <div class="container process__head">
      <p class="eyebrow"><span class="num">03</span> Как мы работаем</p>
      <h2 id="process-title">От вопроса на старте — <em>к цифрам и решениям</em></h2>
    </div>
    <div class="process__viewport">
      <div class="process__rail">
        <svg class="process__track" viewBox="0 0 2400 80" preserveAspectRatio="xMinYMin meet" aria-hidden="true">
          <path class="process__track-bg" d="M0 40H2400"/>
          <path class="process__track-path" d="M0 40H2400"/>
        </svg>
        <?php foreach ($sl['process'] as $i => $st): ?>
        <article class="process__step">
          <div class="process__marker"><span class="mono"><?= sprintf('%02d', $i + 1) ?></span></div>
          <div class="photo process__photo ratio-4x3 <?= $i % 2 ? 'photo--green' : '' ?>"><?= img($st['file'], $st['alt'], ['sizes' => '(min-width: 960px) 420px, 90vw', 'ratio' => '4/3', 'w' => 960, 'h' => 720]) ?></div>
          <div class="process__text">
            <p class="map-meta">Этап <?= $i + 1 ?> · <?= e($st['time']) ?></p>
            <h3><?= e($st['title']) ?></h3>
            <p><?= e($st['text']) ?></p>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section indicators-section" id="indicators" aria-labelledby="indicators-title">
  <div class="container">
    <div class="section-head section-head--split">
      <div><p class="eyebrow">Система индикаторов</p><h2 id="indicators-title">Шесть индикаторов, которые переводят риски <em>в деньги</em></h2></div>
      <p>Оцениваем индикаторы в связке: так формируется укрупнённая рисковая модель проекта на языке финансовых последствий — капзатрат, платежей, резервов, сроков и управленческих решений.</p>
    </div>
    <?= indicators_block() ?>
    <div class="underestimation">
      <h3 class="underestimation__title">Цена недооценки — примеры из нашей практики</h3>
      <ul class="underestimation__list" role="list">
        <?php foreach ($sl['underestimation'] as $u): ?>
        <li class="underestimation__item reveal"><b><?= e($u['title']) ?></b><span><?= e($u['text']) ?></span><span class="underestimation__price mono"><?= e($u['price']) ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="section section--soft map-section" id="map" data-module="map" data-src="<?= asset('data/projects.json') ?>" aria-labelledby="map-title">
  <?= wave('top', 'var(--bg-soft)') ?>
  <div class="container">
    <div class="section-head section-head--split">
      <div><p class="eyebrow"><span class="num">04</span> География</p><h2 id="map-title">Карта <em>проектов</em></h2></div>
      <p><?= count($projects['projects']) ?> проектов с экологическим и социальным блоком из <?= SITE['projects'] ?>+ проектов группы. Нажмите на точку, чтобы увидеть, что мы там делали.</p>
    </div>
    <div class="chips map-filters" role="group" aria-label="Фильтр по типу работ">
      <button class="chip" type="button" data-map-filter="all" aria-pressed="true">Все</button>
      <?php foreach ($projects['types'] as $k => $label): ?>
      <button class="chip" type="button" data-map-filter="<?= e($k) ?>" aria-pressed="false"><span class="dot" style="--c:<?= $tc[$k] ?>"></span><?= e($label) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="map-wrap" data-compass-zone>
      <div class="map" data-map role="region" aria-label="Карта проектов IMC Montan">
        <div class="map__fallback">
          <?= topo_svg('wide') ?>
          <?php /* Статичная схема: равнопромежуточная проекция 25–180° в. д., 36–72° с. ш. */ ?>
          <div class="map__dots">
            <?php foreach ($projects['projects'] as $p): [$lng, $lat] = $p['coords']; ?>
            <button class="map__dot" type="button" data-project="<?= (int) $p['id'] ?>" data-type="<?= e($p['type']) ?>" style="left:<?= round(($lng - 25) / 155 * 100, 2) ?>%;top:<?= round((72 - $lat) / 36 * 100, 2) ?>%;--c:<?= $tc[$p['type']] ?>" aria-label="<?= e($p['title'] . ', ' . $p['region']) ?>"></button>
            <?php endforeach; ?>
          </div>
          <p class="map-meta map__status" data-map-status>Схема проектов · интерактивная карта подгружается</p>
        </div>
      </div>
      <aside class="map-card" data-map-card hidden aria-live="polite"></aside>
      <div class="map-legend map-meta"><span class="north"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3 16 20 12 16 8 20Z" fill="currentColor"/></svg>N</span> Масштаб 1:50 000 000</div>
    </div>
    <details class="map-list">
      <summary>Список проектов (<?= count($projects['projects']) ?>)</summary>
      <ul role="list">
        <?php foreach ($projects['projects'] as $p): ?>
        <li data-type="<?= e($p['type']) ?>"><button type="button" data-project="<?= (int) $p['id'] ?>"><span class="dot" style="background:<?= $tc[$p['type']] ?>"></span><?= e($p['title']) ?> <span class="muted">· <?= e($p['region']) ?>, <?= (int) $p['year'] ?></span></button></li>
        <?php endforeach; ?>
      </ul>
    </details>
  </div>
</section>

<section class="section opinion-section" id="opinion" aria-labelledby="opinion-title">
  <div class="container">
    <div class="section-head section-head--split">
      <div><p class="eyebrow"><span class="num">05</span> Есть мнение</p><h2 id="opinion-title">Позиции команды по острым <em>вопросам отрасли</em></h2></div>
      <p>Коротко — тезис и обоснование. Полностью — в публикациях экспертов IMC Montan в отраслевых журналах и докладах на конференциях.</p>
    </div>
    <figure class="quote-band reveal">
      <div class="quote-band__photo photo"><?= img('gallery/minex-speaker', 'Елена Бурдина выступает с докладом на MINEX Kazakhstan 2026', ['sizes' => '(min-width: 1000px) 360px, 100vw', 'ratio' => '3/2', 'w' => 960, 'h' => 640]) ?></div>
      <blockquote class="quote-band__text"><?= icon('quote') ?><p>Правильно поставленный вопрос на старте проекта или при решении о покупке актива стоит несравнимо меньше, чем ответ на него в процессе строительства и эксплуатации.</p></blockquote>
      <figcaption class="quote-band__by"><?= author_chip(find_by($team, 'slug', 'burdina'), 'author--lg') ?><span class="map-meta">MINEX Kazakhstan 2026, Астана</span></figcaption>
    </figure>
    <div class="grid grid-3 opinion-grid">
      <?php foreach (array_slice($opinions, 0, 3) as $i => $a) echo opinion_card($a, $i); ?>
    </div>
    <div class="flex opinion-section__more"><a class="btn btn--ghost" href="<?= url('opinion/') ?>">Все публикации <?= icon('arrow-right') ?></a></div>
  </div>
</section>

<section class="section section--tight gallery" aria-labelledby="gallery-title">
  <div class="container">
    <div class="section-head section-head--split">
      <div><p class="eyebrow"><span class="num">06</span> Профессиональная жизнь</p><h2 id="gallery-title">Наша профессиональная <em>жизнь</em></h2></div>
      <p>Объекты, которые мы оцениваем, отраслевые форумы и выступления команды. Кликните на кадр, чтобы рассмотреть его.</p>
    </div>
  </div>
  <div class="slider gallery-slider" data-module="slider lightbox" data-lightbox-group="field" data-compass-zone aria-label="Фотогалерея «Наша профессиональная жизнь»">
    <div class="slider__track gallery__track" style="--gap:16px">
      <?php foreach ($sl['gallery'] as $i => $g): ?>
      <figure class="slider__slide gallery__item<?= $i % 3 === 1 ? ' gallery__item--tall' : '' ?>">
        <a href="<?= img_url($g['file'], 1600) ?>" data-lightbox data-title="<?= e($g['caption']) ?>" data-glightbox="title: <?= e($g['caption']) ?>" aria-label="Открыть фото: <?= e($g['alt']) ?>">
          <span class="photo ratio-3x2 <?= $i % 2 ? 'photo--green' : '' ?>"><?= img($g['file'], $g['alt'], ['sizes' => '(min-width: 1000px) 520px, 80vw', 'ratio' => '3/2', 'w' => 960, 'h' => 640]) ?></span>
        </a>
        <figcaption class="map-meta"><?= sprintf('%02d', $i + 1) ?> — <?= e($g['caption']) ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
    <div class="container slider__controls">
      <span class="slider__count" aria-hidden="true"></span>
      <button class="slider__btn" type="button" data-prev aria-label="Назад"><?= icon('arrow-left') ?></button>
      <button class="slider__btn" type="button" data-next aria-label="Вперёд"><?= icon('arrow-right') ?></button>
    </div>
  </div>
</section>

<section class="section team" id="team" aria-labelledby="team-title">
  <div class="container">
    <div class="section-head section-head--split section-head--top">
      <div><p class="eyebrow"><span class="num">07</span> Команда</p><h2 id="team-title">Наша <em>команда</em></h2></div>
      <p>Экологи, социологи, геоэкологи и экономисты природопользования с опытом проектов по российским и международным стандартам. Нажмите на карточку — расскажем подробнее.</p>
    </div>
    <div class="slider team-slider" data-module="slider" aria-label="Команда">
      <div class="slider__track team__track">
        <?php foreach ($team as $i => $m): ?><div class="slider__slide team__slide"><?= team_card($m, $i) ?></div><?php endforeach; ?>
      </div>
      <div class="slider__controls team__controls">
        <span class="slider__dots"></span>
        <button class="slider__btn" type="button" data-prev aria-label="Назад"><?= icon('arrow-left') ?></button>
        <button class="slider__btn" type="button" data-next aria-label="Вперёд"><?= icon('arrow-right') ?></button>
      </div>
    </div>
  </div>
  <?= team_dialogs($team) ?>
</section>

<section class="section contact" id="contact" aria-labelledby="contact-title">
  <?= topo('wide') ?>
  <div class="container contact__grid">
    <div class="contact__intro">
      <p class="eyebrow"><span class="num">08</span> Обсудить проект</p>
      <h2 id="contact-title">Расскажите о&nbsp;задаче — <em>и мы предложим маршрут</em></h2>
      <p class="lead">Опишите проект и стадию, на которой он находится. Если задача не наша — честно скажем и подскажем, к кому обратиться.</p>
      <p class="contact__free"><?= icon('check') ?> <b>Первая консультация и оценка работ — бесплатно</b></p>
      <ul class="contact__points" role="list">
        <li><?= icon('lock') ?> NDA подписываем до получения документов</li>
        <li><?= icon('phone') ?> <a href="tel:<?= e(SITE['phone_raw']) ?>"><?= e(SITE['phone']) ?></a></li>
        <li><?= icon('mail') ?> <a href="mailto:<?= e(SITE['email']) ?>"><?= e(SITE['email']) ?></a></li>
      </ul>
      <div class="contact__expert">
        <?= team_avatars($team) ?>
        <p>На заявку ответит профильный эксперт команды — в зависимости от задачи.</p>
      </div>
    </div>
    <?= contact_form('home') ?>
  </div>
</section>

</main>
<?php require INC . 'footer.php'; ?>
