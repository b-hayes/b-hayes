<?php

declare(strict_types=1);

namespace BHayes\BHayes\Router;

class View implements Response
{
    private static ?View $currentView = null;
    private string $viewFile;
    /**
     * @var mixed|null
     */
    private mixed $data;
    private ?string $layout;
    private int $httpResponseCode;
    private array $headers;

    public function __construct(string $viewFile, mixed $data = null, string $layout = null, $httpResponseCode = 200, $headers = [])
    {
        if (empty($viewFile) || !is_file($viewFile)) throw new \Exception("Cant find View file: '$viewFile' from: " . getcwd());
        if ($layout !== null && !is_file($layout)) throw new \Exception("Cant find Layout file: '$layout' from: " . getcwd());

        $this->viewFile = realpath($viewFile);
        $this->data = $data;
        $this->layout = ($layout)? realpath($layout): null;
        $this->httpResponseCode = $httpResponseCode;
        $this->headers = $headers;
    }

    public static function current(): View
    {
        if (self::$currentView === null) throw new \Exception('There is no current View. Current view is set when body is rendered');
        return self::$currentView;
    }

    public function viewFile(): string
    {
        return $this->viewFile;
    }

    public function data(mixed $data): mixed
    {
        return self::$currentView->data;
    }

    public function code(): int
    {
        return $this->httpResponseCode;
    }

    public function body(): string
    {
        self::$currentView = $this;
        ob_start();
        include $this->layout ?: $this->viewFile;
        $output = ob_get_clean();
        self::$currentView = null;
        return $output;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}