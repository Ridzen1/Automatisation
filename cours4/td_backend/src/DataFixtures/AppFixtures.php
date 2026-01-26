<?php

namespace App\DataFixtures;

use App\Entity\Film;
use App\Entity\Realisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $realisateur = new Realisateur();
            $realisateur->setNom($faker->lastName());
            $realisateur->setPrenom($faker->firstName());

            $manager->persist($realisateur);

            for ($j = 0; $j < mt_rand(2, 5); $j++) {
                $film = new Film();
                $film->setTitre($faker->sentence(3));
                
                $film->setDuree($faker->numberBetween(90, 180) . ' min'); 

                $film->setRealisateur($realisateur);

                $manager->persist($film);
            }
        }

        $manager->flush();
    }
}