<?php

namespace App;

interface QueryBuilderInterface
{
    public function select(array $fields): self;
    public function from(string $table): self;
    public function where(string $condition): self;
    
    public function getSQL(): string;
}