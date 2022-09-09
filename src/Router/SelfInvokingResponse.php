<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class SelfInvokingResponse implements Response, ControllerInterface
{
    protected int $code;
    protected string $reason;
    protected string $body;
    protected array $headers;

    public function __construct(string $body, int $code = 200, string $reason = 'OK', array $headers = [])
    {
        $this->code = $code;
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

    public function __invoke(array $pathSegments, array $data): Response
    {
        return $this;
    }
}