<?php

namespace App\Tests\Unit;

use App\Entity\Film;
use PHPUnit\Framework\TestCase;

class FilmTest extends TestCase
{
    public function testFilmGettersAndSetters(): void
    {
        $film = new Film();
        $titre = "Inception";
        $duree = "148 min";

        $film->setTitre($titre);
        $film->setDuree($duree);

        $this->assertSame($titre, $film->getTitre());
        $this->assertSame($duree, $film->getDuree());
        
        $this->assertNull($film->getRealisateur());
    }
}