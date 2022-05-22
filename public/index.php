<?php

declare(strict_types=1);

use BHayes\BHayes\Controllers\ArticlesController;
use BHayes\BHayes\Router\Renderer;
use BHayes\BHayes\Router\RouteException;
use BHayes\BHayes\Router\Router;
use BHayes\BHayes\Router\CallableResponse;

try {
    //ensure the project root is actually the working dir just in case project is relocated with a global htaccess redirect.
    chdir(__DIR__ . '/..');
    require_once __DIR__ . '/../vendor/autoload.php';

    $renderer = new Renderer();
    $router = new Router();
    $router->add('GET', '/', new CallableResponse(
        (new Parsedown())->text(file_get_contents(__DIR__ . '/../README.md'))
    ));
    $router->add('GET', '/articles/',new ArticlesController());

    //Get && render a response from the requested route.
    try {
        //note: the callables do not have to return a Response object however, that's all I expect.
        $response = $router->invoke($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
        $renderer->render($response);//if it's not a Response object an error will trigger the 500 page.

    } catch (RouteException $routeException) {
        require_once __DIR__ . "/{$routeException->getCode()}.php";
        $errorInfo = [
            'Error' => $routeException->getMessage(),
            ' file' => $routeException->getFile(),
            ' line' => $routeException->getLine(),
            'trace' => $routeException->getTrace()
        ];

        error_log(json_encode($errorInfo));

        //show info for local dev.
        if (str_ends_with($_SERVER['HTTP_HOST'], 'localhost')) {
            echo "<pre style='position: absolute; left: 10px; top: 100vh;'>";
            var_export($errorInfo);
            echo "</pre>";
        }
    }

} catch (\Throwable $error) {
    include __DIR__ . '/500.php';
    $errorInfo = [
        'Error' => $error->getMessage(),
        ' file' => $error->getFile(),
        ' line' => $error->getLine(),
        'trace' => $error->getTrace()
    ];

    error_log(json_encode($errorInfo));

    //show info for local dev.
    if (str_ends_with($_SERVER['HTTP_HOST'], 'localhost')) {
        echo "<pre style='position: absolute; left: 10px; top: 100vh;'>";
        var_export($errorInfo);
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