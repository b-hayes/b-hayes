<?php

declare(strict_types=1);

use BHayes\BHayes\Controllers\ArticlesController;
use BHayes\BHayes\Router\Renderer;
use BHayes\BHayes\Router\RouteException;
use BHayes\BHayes\Router\Router;

try {
    require_once __DIR__ . '/../vendor/autoload.php';

    $renderer = new Renderer();
    $router = new Router();
    $router->add('GET', '/', new \BHayes\BHayes\Controllers\ArticlesController());
    $router->add('GET', '/articles/{fileName}', new \BHayes\BHayes\Controllers\ArticlesController());
    //Get && render a response from the requested route.
    try {
        //access log
        $requestLog = __DIR__ . '/../../' . $_SERVER['HTTP_HOST'] . '-access.log';
        $requestData = json_encode([
            date('D-M-Y h:i:s A'),
            'Request' => [
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $_REQUEST,
                $_FILES,
            ],
            'Client' => [
                Router::clientIpAddress(),
                //Router::clientCountry() //todo: find a way to do this without sending the IP address to some 3rd party.
            ]
        ], JSON_UNESCAPED_SLASHES)
            //fallback to just the URI if json_encode fails.
            ?? $_SERVER['REQUEST_URI'];
        file_put_contents($requestLog, $requestData . "\n", FILE_APPEND);

        $response = $router->invoke();
        $renderer->render($response);
    } catch (RouteException $exception) {
        $potentialErrorPage = __DIR__ . "/{$exception->code()}.php";
        if (is_file($potentialErrorPage)) {
            include $potentialErrorPage;
        }
        //show info for local dev.
        if (str_ends_with($_SERVER['HTTP_HOST'], 'localhost')) {
            echo "<pre style='position: absolute; left: 10px; top: 100vh;'>";
            var_export([
                'Error' => $exception->getMessage(),
                ' file' => $exception->getFile(),
                ' line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ]);
            echo "</pre>";
        }
    }

} catch (\Throwable $error) {
    //This is the last line of defence do not use any dependencies that could break.

    $errorInfo = [//for developers eyes only
        'Error' => $error->getMessage(),
        ' file' => $error->getFile(),
        ' line' => $error->getLine(),
        'trace' => $error->getTrace()
    ];

    //log the error
    error_log(json_encode($errorInfo));

    //construct error response.
    http_response_code(500);
    $errorResponse = ['error' => ['message' => 'Internal server error']];
    $encodingOptions = JSON_UNESCAPED_SLASHES;

    //extra info for developers.
    $developerMode = (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false);
    if ($developerMode) {
        $errorResponse['error_details'] = $errorInfo;
        $encodingOptions = $encodingOptions | JSON_PRETTY_PRINT;
    }

    //respond with JSON if appropriate
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' || $_SERVER['HTTP_ACCEPT'] === 'application/json') {
        echo json_encode($errorResponse, $encodingOptions);
        return;
    }

    //otherwise assume we want a nice html error page.
    include __DIR__ . '/500.php';
    if ($developerMode) {
        echo "<pre style='z-index: 99999999999999999;'>";
        echo json_encode($errorResponse, $encodingOptions);
        echo "</pre>";
    }
}

?>

<style>
    <?=
    //including it via php prevents the page flashing white before the css file is processed.
    include __DIR__ . '/css/global.css';
    ?>
</style>
