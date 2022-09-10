<?php
declare(strict_types=1);

namespace BHayes\BHayes\System;

class File
{
    private string $fileName;

    public function __construct(string $fileName)
    {
        if (!is_file($fileName)) {
            throw new \Exception("Missing file: $fileName");
        }
        $this->fileName = $fileName;
    }

    public function name(): string
    {
        return $this->fileName;
    }

    public function getContents(): string
    {
        return file_get_contents($this->fileName);
    }
}