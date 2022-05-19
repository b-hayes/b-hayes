<?php
declare(strict_types=1);

namespace BHayes\BHayes;

interface Invoker
{
    public function __invoke(array $pathSegments, array $data): Response;
}