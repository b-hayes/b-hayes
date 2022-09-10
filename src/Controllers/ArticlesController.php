<?php
declare(strict_types=1);

namespace BHayes\BHayes\Controllers;

use BHayes\BHayes\Responses\BasicResponse;
use BHayes\BHayes\Router\ControllerInterface;
use BHayes\BHayes\Router\Response;
use BHayes\BHayes\Router\RouteException;
use Parsedown;

class ArticlesController implements ControllerInterface
{
    private string $basePath;
    private string $defaultFile;

    public function __construct(string $folder = '../articles', string $defaultFile = '../README.md')
    {
        $this->basePath = $folder;
        $this->defaultFile = $defaultFile;
    }


    public function __invoke(array $pathSegments, array $data): Response
    {
        $filename = $pathSegments['fileName'] ?? null;
        if (!$filename) {
            $filename = realpath($this->defaultFile);
        } else {
            $filename = realpath($this->basePath . '/' . $filename);
        }
        //no file then it's a 404.
        if (!$filename) throw new RouteException(404);

        $md = (new Parsedown())->text(file_get_contents($filename));
        return new BasicResponse($md);
    }
}