<?php
declare(strict_types=1);

namespace BHayes\BHayes\Controllers;

use BHayes\BHayes\Router\Invoker;
use BHayes\BHayes\Router\Response;
use BHayes\BHayes\Router\RouteException;
use BHayes\BHayes\Router\CallableResponse;
use Parsedown;

class ArticlesController
{
    private array $pathSegments;
    private array $requestData;

    public function __invoke(array $pathSegments, array $requestData)
    {
        $this->pathSegments = $pathSegments;
        $this->requestData = $requestData;
    }

    public function get(string $article): Response
    {
        $fileName = "articles/$article";
        if (!is_file($fileName)) throw new RouteException(404);

        return new CallableResponse(
            (new Parsedown())->text(file_get_contents($fileName))
        );
    }
}