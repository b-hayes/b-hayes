<?php

declare(strict_types=1);

namespace BHayes\BHayes\Router;

use JetBrains\PhpStorm\Pure;

class RouteException extends \Exception implements Response
{
    private int $httpResponseCode;
    private string $reason;

    public function __construct(int $httpResponseCode = 404, string $reason = 'Not Found', ? \Throwable $previous = null)
    {
        $reason = $reason ?? Router::httpStatusFromCode($httpResponseCode);
        parent::__construct($reason, $httpResponseCode, $previous);
        $this->httpResponseCode = $httpResponseCode;
        $this->reason = $reason;
    }

    public function code(): int
    {
        return $this->httpResponseCode;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function body(): string
    {
        return '';
    }

    public function headers(): array
    {
        return [];
    }
}