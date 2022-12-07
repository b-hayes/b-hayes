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

    public function __construct(string $folder = 'articles', string $defaultFile = '../README.md')
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
        //todo: create some view rendering solution.
        $md = <<<HTML
<main class="article" style="max-width: 1000px; margin: auto; box-shadow: 0 0 34px 20px rgba(0,0,0,0.79); padding: 30px">
$md
</main>
<style>
main .article {
    max-width: 1000px;
    margin: auto;
    box-shadow: 0 0 34px 20px rgba(0,0,0,0.79);
    padding: 30px;
}
main img{
    max-width: 100%;    
}
</style>
HTML;

        //strip the word public out of the url paths in the links to make the web servers url paths.
        $md = str_replace(['img src="public/', 'href="public/'], ['img src="', 'href="'], $md);

        return new BasicResponse($md);
    }
}
