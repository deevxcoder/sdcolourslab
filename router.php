<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/api/docs' || $uri === '/api/docs/') {
    require __DIR__ . '/api/docs.php';
    return true;
}

if (str_starts_with($uri, '/api')) {
    require __DIR__ . '/api/index.php';
    return true;
}

return false;
