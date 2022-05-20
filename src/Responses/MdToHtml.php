<?php
declare(strict_types=1);

namespace BHayes\BHayes\Responses;

use BHayes\BHayes\Router\SelfInvokingResponse;
use BHayes\BHayes\System\File;
use Parsedown;

class MdToHtml extends SelfInvokingResponse
{
    private File $file;

    /**
     * @throws \Exception
     */
    public function __construct(string $file, int $code = 200, string $reason = 'OK', array $headers = [])
    {
        $this->file = new File($file);
        parent::__construct('', $code, $reason, $headers);
    }

    public function body(): string
    {
        if (empty($this->body)) {
            $this->body = (new Parsedown())->text($this->file->getContents());
        }

        return $this->body;
    }
}