<?php
/**
 * Генератор sitemap.xml. Запуск после добавления материалов:
 *   php sitemap.php https://<домен> > sitemap.xml
 * Или откройте /sitemap.php в браузере — получите актуальную карту на лету.
 */
$cliOrigin = $argv[1] ?? null;
if ($cliOrigin) { $p = parse_url($cliOrigin); $_SERVER['HTTP_HOST'] = $p['host']; $_SERVER['HTTPS'] = ($p['scheme'] ?? 'https') === 'https' ? 'on' : 'off'; $_SERVER['DOCUMENT_ROOT'] = __DIR__; }
require_once __DIR__ . '/assets/include/config.php';
$urls = [['', 'weekly', '1.0'], ['about/', 'monthly', '0.8'], ['services/', 'monthly', '0.9'], ['news/', 'weekly', '0.7'], ['opinion/', 'weekly', '0.7'], ['contacts/', 'yearly', '0.7'], ['feedback/', 'yearly', '0.4'], ['privacy/', 'yearly', '0.2']];
foreach (data('services') as $s) $urls[] = ['services/' . $s['slug'] . '/', 'monthly', '0.9'];
// Кейсы, статьи и подкасты скрыты (демо-контент) — в карту не попадают
foreach (data('news') as $n) $urls[] = ['news/' . $n['slug'] . '/', 'yearly', '0.4', $n['date']];
foreach (data('articles') as $a) if ($a['type'] === 'opinion') $urls[] = ['opinion/' . $a['slug'] . '/', 'yearly', '0.6', $a['date']];
if (PHP_SAPI !== 'cli') header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . e(abs_url($u[0])) . '</loc>' . (isset($u[3]) ? '<lastmod>' . e($u[3]) . '</lastmod>' : '') . '<changefreq>' . $u[1] . '</changefreq><priority>' . $u[2] . "</priority></url>\n";
}
echo "</urlset>\n";
