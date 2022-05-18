<?php
declare(strict_types=1);

namespace BHayes\BHayes;

interface Response
{
    public function code(): int;
    
    public function reason(): string;

    public function body();

    public function headers();

    public function render(): string;
}