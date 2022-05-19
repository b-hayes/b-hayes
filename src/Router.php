<?php
declare(strict_types=1);

namespace BHayes\BHayes;

class Router
{
    private array $routes = [];

    public function invoke(string $method, string $path): Response
    {
        /**
         * @var $invoker Invoker
         */
        $invoker  = $this->routes[$method][$path];
        $segments = array_filter(explode('/', $path));
        return $invoker($segments, $_REQUEST);
    }

    public function add(string $method, string $path, Invoker $invoker)
    {
        $this->routes[$method][$path] = $invoker;
    }

    public static function requestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Attempts to get the clients real ip address even if they are behind a proxy.
     * @return mixed
     */
    public static function clientIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR'];
    }
}