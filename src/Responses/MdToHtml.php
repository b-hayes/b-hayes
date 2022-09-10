<?php
declare(strict_types=1);

namespace BHayes\BHayes\Responses;

use BHayes\BHayes\Router\BasicResponse;
use BHayes\BHayes\System\File;
use Parsedown;

class MdToHtml extends BasicResponse
{
    private File $file;

    /**
     * @throws \Exception
     */
    public function __construct(string $file, int $httpResponseCode = 200, string $reason = 'OK', array $headers = [])
    {
        $this->file = new File($file);
        parent::__construct('', $httpResponseCode, $reason, $headers);
    }

    public function body(): string
    {
        if (empty($this->body)) {
            $this->body = (new Parsedown())->text($this->file->getContents());
        }

        return $this->body;
    }
}