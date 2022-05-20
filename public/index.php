<?php

declare(strict_types=1);

use BHayes\BHayes\Router\Renderer;
use BHayes\BHayes\Router\ResponseException;
use BHayes\BHayes\Router\Router;
use BHayes\BHayes\Router\SelfInvokingResponse;

try {
    chdir(__DIR__);//ensure a consistent working dir just in case.
    require_once __DIR__ . '/../vendor/autoload.php';

    $renderer = new Renderer();
    $router = new Router();
    $router->add('GET', '/', new SelfInvokingResponse(
        (new Parsedown())->text(file_get_contents(__DIR__ . '/../README.md'))
    ));
    //for now lets just make the article link work
    $router->add('GET', '/', new SelfInvokingResponse(
        (new Parsedown())->text(file_get_contents(__DIR__ . '/../README.md'))
    ));

    //Get && render a response from the requested route.
    try {
        $response = $router->invoke($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
        $renderer->render($response);

    } catch (ResponseException $responseException) {

        $renderer->render($responseException);

        $potentialErrorPage = __DIR__ . "/{$responseException->code()}.php";
        if (is_file($potentialErrorPage)) {
            include $potentialErrorPage;
        }
    }

} catch (\Throwable $error) {
    include __DIR__ . '/500.php';
    //show info for local dev.
    if (str_ends_with($_SERVER['HTTP_HOST'], 'localhost')) {
        echo "<pre style='position: absolute; left: 10px; top: 100vh;'>";
        var_export([
            'Error' => $error->getMessage(),
            ' file' => $error->getFile(),
            ' line' => $error->getLine(),
            'trace' => $error->getTrace()
        ]);
        echo "</pre>";
    }
}

?>

<style>
    <?=
    //doing it this way first prevents the page flashing white first in dark mode while it waits to download the separate css file.
    file_get_contents(__DIR__ . '/css/global.css') ?>
</style>

<link rel="stylesheet" href="/css/global.css">