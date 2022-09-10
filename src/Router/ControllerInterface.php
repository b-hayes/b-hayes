<?php
declare(strict_types=1);

namespace BHayes\BHayes\Router;

interface ControllerInterface
{
    public function __invoke(array $pathSegments, array $data): Response;
}