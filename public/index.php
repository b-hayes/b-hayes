<?php

declare(strict_types=1);
try {
    chdir(__DIR__);//ensure a consistent working dir just in case.
    require_once __DIR__ . '/../vendor/autoload.php';

    if ($_SERVER['REQUEST_URI'] === '/') {
        $parsedown = new Parsedown();
        echo $parsedown->text(file_get_contents(__DIR__ . '/../README.md'));
        return;
    }

    include __DIR__ . '/404.php';

} catch (\Throwable $exception) {
    include __DIR__ . '/500.php';
}

?>
<style>
    <?= file_get_contents(__DIR__ . '/css/global.css') ?>
</style>