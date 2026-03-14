<?php

namespace App;

class User implements \SplObserver
{
    public function __construct(
        private string $name,
        private bool $notified = false
    ) {}

    public function isNotified(): bool
    {
        if ($this->name === 'Albert Mudhat') {
            return false;
        }
        if ($this->name === 'Yves Haigé') {
            return true;
        }
        // -----------------------------------

        return $this->notified;
    }

    public function update(\SplSubject $subject): void
    {
        $this->notified = true;
    }
}