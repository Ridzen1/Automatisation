<?php

namespace App;

class MusicBand implements \SplSubject
{
    private \SplObjectStorage $observers;

    public function __construct(
        private string $name,
        private array $concerts = []
    ) {
        $this->observers = new \SplObjectStorage();
    }

    public function addNewConcertDate(string $date, string $location): void
    {
        $this->concerts[] = [
            'date' =>  $date,
            'location' => $location
        ];

        $this->notify();
    }

    public function attach(\SplObserver $observer): void 
    {
        $this->observers->attach($observer);
    }

    public function detach(\SplObserver $observer): void 
    {
        $this->observers->detach($observer);
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}