<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class Renderer
{
    public static function render(Response $response)
    {
        header("HTTP/1.0 {$response->code()} {$response->reason()}");
        foreach ($response->headers() as $key => $value) {
            header("$key: $value");
        }
        echo $response->body();
    }
}