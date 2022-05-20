<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

interface Response
{
    public function code(): int;

    public function reason(): string;

    public function body(): string;

    /**
     * @return string[]
     */
    public function headers(): array;
}