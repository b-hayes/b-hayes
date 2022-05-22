<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

class CallableResponse implements Response
{
    public function __invoke(array $pathSegments, array $data): Response
    {
        return $this;
    }

    public function __construct(
        protected string $body,
        protected int    $code = 200,
        protected string $reason = 'ok',
        protected array  $headers = []
    )
    {
    }

    public function body(): string
    {
        return $this->body;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}