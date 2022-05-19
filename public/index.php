<?php

declare(strict_types=1);

use BHayes\BHayes\SelfInvokingResponse;

try {
    chdir(__DIR__);//ensure a consistent working dir just in case.
    require_once __DIR__ . '/../vendor/autoload.php';

    $renderer = new \BHayes\BHayes\Renderer();
    $router = new \BHayes\BHayes\Router();
    $router->add('GET', '/', new SelfInvokingResponse(
        (new Parsedown())->text(file_get_contents(__DIR__ . '/../README.md'))
    ));

    //Get && render a response from the requested route.
    try {
        $response = $router->invoke($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
        $renderer->render($response);

    } catch (\BHayes\BHayes\ResponseException $responseException) {

        $renderer->render($responseException);

        $potentialErrorPage = __DIR__ . "/{$responseException->code()}.php";
        if (is_file($potentialErrorPage)) {
            include $potentialErrorPage;
        }
    }

} catch (\Throwable $error) {
    include __DIR__ . '/500.php';
}

?>

<style>
    <?=
    //doing it this way first prevents the page flashing white first in dark mode while it waits to download the separate css file.
    file_get_contents(__DIR__ . '/css/global.css') ?>
</style>

<link rel="stylesheet" href="/css/global.css">