<?php

declare(strict_types=1);
try {
    require_once __DIR__ . '/../vendor/autoload.php';

    $parsedown = new Parsedown();
    echo $parsedown->text(file_get_contents(__DIR__ . '/../README.md'));

} catch (\Throwable $exception) {
    include __DIR__ . '/500.php';
}

?>
<style>
    <?= file_get_contents(__DIR__ . '/css/global.css') ?>
</style>