<?php
/** RSS-лента «Есть мнение» и новостей: /rss.xml (через .htaccess) */
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
require_once __DIR__ . '/assets/include/config.php';
$items = array_values(array_filter(data('articles'), fn($a) => $a['type'] === 'opinion'));
foreach (data('news') as $n) $items[] = ['type' => 'news', 'slug' => $n['slug'], 'title' => $n['title'], 'date' => $n['date'], 'excerpt' => $n['excerpt'], 'author' => '', 'tags' => ['новости'], 'cover' => $n['image']];
usort($items, fn($a, $b) => strcmp($b['date'], $a['date']));
header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
  <title>IMC Montan — новости и «Есть мнение»</title>
  <link><?= e(abs_url('')) ?></link>
  <atom:link href="<?= e(abs_url('rss.xml')) ?>" rel="self" type="application/rss+xml"/>
  <description>Новости и позиции экспертов IMC Montan об экологическом и социальном сопровождении горных проектов</description>
  <language>ru</language>
  <lastBuildDate><?= date(DATE_RSS, strtotime($items[0]['date'] ?? 'now')) ?></lastBuildDate>
<?php foreach ($items as $a): $link = abs_url(($a['type'] === 'news' ? 'news/' : 'opinion/') . $a['slug'] . '/'); $author = find_by(data('team'), 'slug', $a['author']); ?>
  <item>
    <title><?= e($a['title']) ?></title>
    <link><?= e($link) ?></link>
    <guid isPermaLink="true"><?= e($link) ?></guid>
    <pubDate><?= date(DATE_RSS, strtotime($a['date'])) ?></pubDate>
    <description><?= e($a['excerpt']) ?></description>
    <?php if ($author): ?><author><?= e(SITE['email'] . ' (' . $author['name'] . ')') ?></author><?php endif; ?>
    <?php foreach ($a['tags'] as $t): ?><category><?= e($t) ?></category><?php endforeach; ?>
    <enclosure url="<?= e(SITE_ORIGIN . img_url($a['cover'], 960)) ?>" type="image/webp" length="0"/>
  </item>
<?php endforeach; ?>
</channel>
</rss>
