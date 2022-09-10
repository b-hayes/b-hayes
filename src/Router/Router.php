<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

use BHayes\Json\ComposerJson;
use function PHPUnit\Framework\throwException;

class Router
{
    const COMPOSER_AUTOLOAD_CONTROLLERS = '{composer-autoload}/Controllers';
    private array $routes = [];
    private string $controllerNameSpace;
    private string $appendToClassName;

    public function __construct(string $controllerNameSpace, string $appendToClassName = "Controller")
    {
        $this->controllerNameSpace = $controllerNameSpace;
        $this->appendToClassName = $appendToClassName;
    }

    public static function getServerUriPath(): string
    {
        return  rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/\\');
    }

    public function invoke(string $path = null, string $method = 'GET'): Response
    {
        if ($path === null) $path = self::getServerUriPath();
        $controller = $this->determineController($path, $method);
        $segments = explode('/', $path);
        return $controller($segments, $_REQUEST);
    }

    public function add(string $method, string $path, ControllerInterface $invoker)
    {
        $this->routes[$method][$path] = $invoker;
    }

    public static function clientIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR'];
    }

    private function determineController(string $path, string $method): ControllerInterface
    {
        $translated = $this->controllerNameSpace . '\\' . str_replace('/', '\\', $path);
        if (class_exists($translated)) return new $translated();

        throw new RouteException("$path not found", 404);
    }
}