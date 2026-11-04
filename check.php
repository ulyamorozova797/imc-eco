<?php
/*
 * Диагностика хостинга IMC Montan.
 * Откройте https://ваш-домен.ru/check.php — увидите версию PHP, расширения, права и
 * первую ошибку, из-за которой может быть 500. ПОСЛЕ ПРОВЕРКИ УДАЛИТЕ ЭТОТ ФАЙЛ.
 * Написан без новых возможностей языка, чтобы работать даже на старом PHP.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

$ok = '<b style="color:#1D6B57">✔</b>';
$bad = '<b style="color:#B3362F">✘</b>';
$rows = array();

$v = PHP_VERSION;
$rows[] = array('Версия PHP', $v, version_compare($v, '7.4.0', '>='), 'Нужно 7.4 или новее, рекомендуем 8.2–8.3');
foreach (array('mbstring', 'json', 'openssl', 'gd') as $ext) {
    $rows[] = array('Расширение ' . $ext, extension_loaded($ext) ? 'есть' : 'нет', extension_loaded($ext) || $ext === 'gd', $ext === 'mbstring' ? 'обязательно' : ($ext === 'openssl' ? 'нужно для SMTP' : ''));
}
$root = dirname(__FILE__);
$rows[] = array('Папка сайта', $root, is_file($root . '/assets/include/config.php'), 'Здесь должны лежать index.php и папка assets');
$rows[] = array('.htaccess на месте', is_file($root . '/.htaccess') ? 'да' : 'нет', is_file($root . '/.htaccess'), 'Скрытый файл: проверьте, что он загрузился');
$rows[] = array('Запись в assets/data', is_writable($root . '/assets/data') ? 'можно' : 'нельзя', is_writable($root . '/assets/data'), 'Нужна для индекса поиска (права 755)');
$rows[] = array('DOCUMENT_ROOT', isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '—', true, '');
$rows[] = array('mod_rewrite', function_exists('apache_get_modules') ? (in_array('mod_rewrite', apache_get_modules()) ? 'включён' : 'выключен') : 'не определить (PHP как CGI — это нормально)', true, 'Проверка: откройте /news/field-season-2026/');

echo '<!doctype html><meta name="viewport" content="width=device-width"><title>Проверка хостинга</title>';
echo '<body style="font:16px/1.5 system-ui,sans-serif;max-width:880px;margin:40px auto;padding:0 16px;color:#0E1A24">';
echo '<h1 style="font-family:Georgia,serif">Проверка хостинга IMC Montan</h1><table style="border-collapse:collapse;width:100%">';
foreach ($rows as $r) {
    echo '<tr style="border-bottom:1px solid #ddd"><td style="padding:8px">' . ($r[2] ? $ok : $bad) . '</td><td style="padding:8px">' . htmlspecialchars($r[0]) . '</td><td style="padding:8px"><code>' . htmlspecialchars($r[1]) . '</code></td><td style="padding:8px;color:#5A6B78;font-size:14px">' . htmlspecialchars($r[3]) . '</td></tr>';
}
echo '</table>';

echo '<h2 style="font-family:Georgia,serif">Пробный запуск главной</h2>';
if (version_compare($v, '7.4.0', '<')) {
    echo '<p>' . $bad . ' Пропущено: сначала переключите PHP на 8.2 или 8.3 в панели хостинга.</p>';
} else {
    ob_start();
    $err = null;
    try {
        include $root . '/index.php';
    } catch (Throwable $e) {
        $err = get_class($e) . ': ' . $e->getMessage() . ' в ' . str_replace($root, '', $e->getFile()) . ':' . $e->getLine();
    }
    $html = ob_get_clean();
    if ($err) echo '<p>' . $bad . ' Ошибка: <code>' . htmlspecialchars($err) . '</code></p>';
    elseif (strpos($html, '</html>') !== false) echo '<p>' . $ok . ' Главная собирается без ошибок (' . round(strlen($html) / 1024) . ' КБ HTML). Если в браузере всё ещё 500 — причина в .htaccess: закомментируйте строку <code>Options -Indexes</code>.</p>';
    else echo '<p>' . $bad . ' Страница оборвалась. Последние строки вывода:</p><pre style="white-space:pre-wrap;background:#f6f3ec;padding:12px">' . htmlspecialchars(substr(strip_tags($html), -1500)) . '</pre>';
}
echo '<p style="margin-top:32px;padding:12px 16px;background:#FBE9E7;border-radius:8px"><b>Удалите check.php после проверки.</b></p></body>';
