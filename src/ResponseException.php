<?php
declare(strict_types=1);

namespace BHayes\BHayes;

class ResponseException extends \Exception implements Response
{
    private string $body;
    private array $headers;

    public function __construct(string $body = '', int $code = 404, string $reason = 'Not found.', array $headers = [])
    {
        $this->body = $body;
        $this->headers = $headers;
        parent::__construct($reason, $code);
    }

    public function code(): int
    {
        return $this->code;
    }

    public function reason(): string
    {
        return $this->message;
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