<?php
/**
 * Роутер для встроенного сервера PHP (только для локальной разработки):
 *   php -S localhost:8000 router.php
 * Повторяет правила .htaccess: ЧПУ для новостей, статей, мнений, подкастов, кейсов, 404.
 * На Apache-хостинге этот файл не используется.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$root = __DIR__;
$map = ['news', 'articles', 'opinion', 'podcasts', 'cases'];

// запрет прямого доступа к служебным PHP
if (preg_match('~^/assets/(include|data|html)/.*\.php$~', $uri)) { http_response_code(403); exit('Forbidden'); }
if ($uri === '/rss.xml') { require $root . '/rss.php'; return true; }
if ($uri === '/sitemap.xml') { require $root . '/sitemap.php'; return true; }
if ($uri === '/robots.txt') { require $root . '/robots.php'; return true; }

$file = $root . $uri;
if ($uri !== '/' && is_file($file)) return false; // статика как есть
if (is_dir($file) && is_file(rtrim($file, '/') . '/index.php')) {
    if (substr($uri, -1) !== '/') { header('Location: ' . $uri . '/', true, 301); exit; }
    $_SERVER['SCRIPT_NAME'] = rtrim($uri, '/') . '/index.php';
    chdir(rtrim($file, '/'));
    require rtrim($file, '/') . '/index.php';
    return true;
}
foreach ($map as $sec) {
    if (preg_match('~^/' . $sec . '/([a-z0-9\-]+)/?$~', $uri, $m) && $m[1] !== 'item') {
        $_GET['slug'] = $m[1];
        require "$root/$sec/item/index.php";
        return true;
    }
}
http_response_code(404);
require $root . '/404/index.php';
return true;
