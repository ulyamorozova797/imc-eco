<?php
/** robots.txt (отдаётся по /robots.txt через .htaccess) — домен подставляется автоматически */
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
require_once __DIR__ . '/assets/include/config.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
echo "Disallow: " . BASE_URL . "assets/include/\n";
echo "Disallow: " . BASE_URL . "assets/php/\n";
echo "Disallow: " . BASE_URL . "check.php\n";
echo "Disallow: " . BASE_URL . "cases/\n"; // кейсы скрыты до появления реальных материалов
echo "Disallow: /*?q=\n";
echo "Clean-param: utm_source&utm_medium&utm_campaign&utm_content&utm_term&yclid\n";
echo "Allow: " . BASE_URL . "\n\n";
echo "Sitemap: " . abs_url('sitemap.xml') . "\n";
