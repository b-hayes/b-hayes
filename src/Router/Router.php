<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

use BHayes\Json\ComposerJson;
use phpDocumentor\Reflection\Types\True_;
use function PHPUnit\Framework\throwException;

class Router
{
    const COMPOSER_AUTOLOAD_CONTROLLERS = '{composer-autoload}/Controllers';
    private array $routes = [];
    private string $controllerNameSpace;
    private string $appendToClassName;

    public function __construct(string $controllerNameSpace = '', string $appendToClassName = "Controller")
    {
        $this->controllerNameSpace = $controllerNameSpace;
        $this->appendToClassName = $appendToClassName;
    }

    public static function requestContentType()
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
        $responseGenerator = $this->routes[$method][$path]
            ?? $this->resolveComplexRoute($path, $method)
            ?? throw new RouteException(404);

        $segments = explode('/', $path);

        return $responseGenerator($segments, $_REQUEST);
    }

    public function add(string $method, string $path, callable $responseGenerator): void
    {
        assert(str_starts_with($path, '/'), 'Route path should always start with "/"');
        if ($path === '/' || !str_contains($path, '{')) {
            //this URI can only be an exact match anyway so let just keep it as is for faster route matching.
            $this->routes[$method][$path] = $responseGenerator;
            return;
        }

        //converts the "/uri/path" into an array["uri"]["path"] = $responseGenerator
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

    private function resolveComplexRoute(string $path, string $method): ControllerInterface
    {
        throw new RouteException(404);
    }
}