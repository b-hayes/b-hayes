<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class BasicResponse implements Response
{
    protected int $code;
    protected string $reason;
    protected string $body;
    protected array $headers;

    public function __construct(string $body, int $httpResponseCode = 200, string $reason = 'OK', array $headers = [])
    {
        $this->code = $httpResponseCode;
        $this->reason = $reason;
        $this->body = $body;
        $this->headers = $headers;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}