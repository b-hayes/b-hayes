<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class Router
{
    const COMPOSER_AUTOLOAD_CONTROLLERS = '{composer-autoload}/Controllers';
    private array $routes = [];

    public static function requestContentType(): string
    {
        return $_SERVER['HTTP_ACCEPT'];
    }

    public static function requestUriPath(): string
    {
        if ($_SERVER['REQUEST_URI'] === '/') return $_SERVER['REQUEST_URI'];
        return  rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/\\');
    }

    public function invoke(string $path = null, string $method = null): Response
    {
        if ($path === null) $path = self::requestUriPath();
        if ($method === null) $method = $_SERVER['REQUEST_METHOD'];
        //exact match
        $controller = $this->routes[$method][$path] ?? null;
        $segments = explode('/', $path);
        array_shift($segments);//always an empty one at the start due to the first '/'

        //complex match
        if (!$controller) {
            list($controller, $segments) = $this->resolveComplexRoute($segments,$method);
        }

        return $controller($segments, $_REQUEST);
    }

    public function add(string $method, string $path, callable $responseGenerator): void
    {
        assert(str_starts_with($path, '/'), 'Route path should always start with "/"');
        assert(!str_contains($path, ':'), 'Typed/regex routes not supported.');
        if (!str_contains($path, '{')) {
            //no variance in this path so we can avoid iterating routes to match it later.
            $this->routes[$method][$path] = $responseGenerator;
            return;
        }

        //converts the "/uri/paths" into a tree so that iterating all of is no necessary to match them later.
        $explode =array_filter( explode('/', $path));
        $arrayPath = '["' . implode('"]["', $explode) . '"]';
        $code = "\$this->routes[\$method]$arrayPath = \$responseGenerator;";
        eval($code);
    }

    public static function clientIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @param array $segments
     * @param string $method
     * @return array{ controller: ControllerInterface, segments: string[] }
     * @throws RouteException
     */
    private function resolveComplexRoute(array $segments, string $method): array
    {
        echo "<pre>";
        $collected = [];
        $controller = null;
        $routes = $this->routes[$method] ?? [];
        foreach ($segments as $seg) {
            //direct match
            if (isset($routes[$seg])) {
                $controller = $routes = $routes[$seg];
                $collected[] = $seg;
                //echo "$seg was matched!\n";
                continue;
            }
            //variable match
            $routes = array_filter($routes, fn($key) => str_contains($key, '{'), ARRAY_FILTER_USE_KEY);
            if (count($routes) === 1) {
                $varName = array_key_first($routes);
                $collected[trim($varName,'{}')] = $seg;
                $controller = $routes = $routes[$varName];
                //echo "$seg was matched with $varName!\n";
                continue;
            } elseif(count($routes) > 1) {
                throw new \Exception("'$seg' matches more than one route: " . json_encode($routes));
            }
            //echo "no match for $seg. Stopped looking.\n";
            throw new RouteException(404);
        }
        if (!$controller instanceof ControllerInterface) throw new RouteException(404);

        return [$controller, $collected];
    }
}