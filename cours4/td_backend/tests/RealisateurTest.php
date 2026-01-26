<?php

namespace App\Tests\Unit;

use App\Entity\Realisateur;
use App\Entity\Film;
use PHPUnit\Framework\TestCase;

class RealisateurTest extends TestCase
{
    public function testRealisateurGettersAndSetters(): void
    {
        $realisateur = new Realisateur();
        $nom = "Spielberg";
        $prenom = "Steven";

        $realisateur->setNom($nom);
        $realisateur->setPrenom($prenom);

        $this->assertSame($nom, $realisateur->getNom());
        $this->assertSame($prenom, $realisateur->getPrenom());

        
        $this->assertTrue($realisateur->getFilms()->isEmpty());

        $film = new Film();
        $realisateur->addFilm($film);

        $this->assertCount(1, $realisateur->getFilms());
        
        $this->assertSame($realisateur, $film->getRealisateur());
    }
}