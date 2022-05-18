<?php

declare(strict_types=1);
try {
    chdir(__DIR__);//ensure a consistent working dir just in case.
    require_once __DIR__ . '/../vendor/autoload.php';

    if ($_SERVER['REQUEST_URI'] === '/') {
        $parsedown = new Parsedown();
        echo $parsedown->text(file_get_contents(__DIR__ . '/../README.md'));
    } else {
        include __DIR__ . '/404.php';
    }

} catch (\Throwable $exception) {
    include __DIR__ . '/500.php';
}

?>

<style>
    <?=
    //doing it this way first prevents the page flashing white first in dark mode while it waits to download the separate css file.
    file_get_contents(__DIR__ . '/css/global.css') ?>
</style>

<link rel="stylesheet" href="/css/global.css">