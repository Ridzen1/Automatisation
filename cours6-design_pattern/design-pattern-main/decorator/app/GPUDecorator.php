<?php

namespace App;

class GPUDecorator implements Computer
{
    protected Computer $computer;

    public function __construct(Computer $computer)
    {
        $this->computer = $computer;
    }

    public function getPrice(): int
    {
        return $this->computer->getPrice() + 250;
    }

    public function getDescription(): string
    {
        return $this->computer->getDescription() . ", with a GPU";
    }
}