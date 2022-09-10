<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

interface Response
{
    const OK = 200;
    const NOT_FOUND = 404;
    //todo: add more response codes.

    public function code(): int;

    public function body(): string;

    /**
     * @return string[]
     */
    public function headers(): array;
}