<?php
declare(strict_types=1);

namespace BHayes\BHayes;

interface Response
{
    /**
     * @param int      $code
     * @param string   $reason
     * @param string   $body
     * @param string[] $headers int the form of  ['key' => 'value']
     */
    public function __construct(string $body, int $code, string $reason, array $headers);

    public function code(): int;

    public function reason(): string;

    public function body(): string;

    /**
     * @return string[]
     */
    public function headers(): array;
}