<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

use function Composer\Autoload\includeFile;

class Router
{
    private array $routes = [];

    /**
     * @throws RouteException
     */
    public function invoke(string $path, string $method = 'GET'): Response
    {
        $invoker = null;
        if (isset($this->routes[$method][$path])) {
            $invoker = $this->routes[$method][$path];//direct match no processing required.
        } else {
            foreach ($this->routes[$method] as $route => $handler) {
                //partial match eg. '/cats' matches '/cats/catId' or '/cats/catId/legs' (invoker needs to handle remaining segments)
                if (str_starts_with($path, $route)) {
                    $invoker = $handler;
                    //reminder of path that didn't match will become function parameters
                    $unmached = substr($path, strlen($route));
                }
            }
        }

        if (!$invoker) {
            throw new RouteException(404);
        }

        //base __invoker is always called since the controller may need this data injected before the more specific method is called.
        $segments = array_filter(explode('/', $path));
        $response = $invoker($segments, $_REQUEST);

        //Advanced handler.
        if (method_exists($invoker, $method)) {
            $params = array_filter(explode('/', $unmached ?? ''));
            $response =[$invoker, $method](...$params);
        }

        return $response;
    }

    public function add(string $method, string $path, callable $invoker)
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