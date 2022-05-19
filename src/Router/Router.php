<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class Router
{
    private array $routes = [];

    /**
     * @throws ResponseException
     */
    public function invoke(string $path, string $method = 'GET'): Response
    {
        /**
         * @var $invoker Invoker
         */
        $invoker  = $this->routes[$method][$path] ?? null;
        if (!$invoker) {
            throw new ResponseException();
        }

        $segments = array_filter(explode('/', $path));

        //Basic REST API support.
        if ($method !== 'GET') {
            if (! method_exists($invoker, $method)) {
                throw new ResponseException('', 405, 'Method not allowed.');
            }
            return $invoker->{$method}($segments, $_REQUEST);
        }

        return $invoker($segments, $_REQUEST);
    }

    public function add(string $method, string $path, Invoker $invoker)
    {
        $this->routes[$method][$path] = $invoker;
    }

    public static function clientIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR'];
    }
}