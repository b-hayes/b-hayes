<?php

declare(strict_types=1);
try {
    require_once __DIR__ . '/../vendor/autoload.php';

    echo '<pre>', basename(__DIR__) . DIRECTORY_SEPARATOR . basename(__FILE__), '</pre>';
    echo '<pre>', basename(getcwd()), ' ', $_SERVER['HTTP_HOST'], '</pre>';

    $parsedown = new Parsedown();
    echo $parsedown->text(file_get_contents(__DIR__ . '/../README.md'));

} catch (\Throwable $exception) {
    include __DIR__ . '/500.php';
}

?>

<link rel="stylesheet" type="text/css" href="/css/global.css">
