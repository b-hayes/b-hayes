<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class Renderer
{
    public static function render(Response $response): void
    {
        header('HTTP/1.0 ' . $response->code() . ' ' . self::httpStatusFromCode($response->code()) );
        foreach ($response->headers() as $key => $value) {
            header("$key: $value");
        }
        echo $response->body();
    }

    public static function httpStatusFromCode(int $httpResponseCode): string
    {
        return match ($httpResponseCode) {
            200 => 'OK',
            404 => 'Not Found',
            //todo: fill out all the response codes.
            default => 'Unknown'
        };
    }
}