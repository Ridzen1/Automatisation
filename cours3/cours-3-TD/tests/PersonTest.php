<?php

namespace Tests;

use App\Entity\Person;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    // Test simple : Est-ce que le nom et le wallet se créent bien ?
    public function testCreation(): void
    {
        $person = new Person('Alice', 'EUR');
        $this->assertEquals('Alice', $person->getName());
        $this->assertEquals('EUR', $person->getWallet()->getCurrency());
        $this->assertEquals(0, $person->getWallet()->getBalance());
    }

    // Test simple : Ajouter des sous et vérifier que hasFund fonctionne
    public function testHasFund(): void
    {
        $person = new Person('Alice', 'EUR');
        
        // On met 10 euros, donc il doit avoir des fonds (True)
        $person->getWallet()->addFund(10);
        $this->assertTrue($person->hasFund());
    }

    // Test simple : Transférer 50€ d'une poche à l'autre
    public function testTransfertSimple(): void
    {
        $alice = new Person('Alice', 'EUR');
        $bob = new Person('Bob', 'EUR');
        
        $alice->getWallet()->addFund(100); // Alice a 100

        $alice->transfertFund(50, $bob); // Elle donne 50 à Bob

        $this->assertEquals(50, $alice->getWallet()->getBalance()); // Il reste 50 à Alice
        $this->assertEquals(50, $bob->getWallet()->getBalance());   // Bob a reçu 50
    }

    // Test simple : Diviser 100€ en 2 personnes (Chiffres ronds !)
    public function testDivisionSimple(): void
    {
        $alice = new Person('Alice', 'EUR');
        $alice->getWallet()->addFund(100);

        $bob = new Person('Bob', 'EUR');
        $charlie = new Person('Charlie', 'EUR');

        // 100 divisé par 2 = 50 chacun. Pas de virgule, pas d'erreur.
        $alice->divideWallet([$bob, $charlie]);

        $this->assertEquals(0, $alice->getWallet()->getBalance());
        $this->assertEquals(50, $bob->getWallet()->getBalance());
        $this->assertEquals(50, $charlie->getWallet()->getBalance());
    }

    // Test simple : Acheter un produit
    public function testAchatProduit(): void
    {
        $alice = new Person('Alice', 'USD');
        $alice->getWallet()->addFund(50);
        
        // Un livre à 20 USD
        $product = new Product('Livre', ['USD' => 20], 'other');

        $alice->buyProduct($product);

        // 50 - 20 = 30. Simple.
        $this->assertEquals(30, $alice->getWallet()->getBalance());
    }
}