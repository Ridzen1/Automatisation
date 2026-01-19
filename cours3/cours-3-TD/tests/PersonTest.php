<?php

namespace Tests;

use App\Entity\Person;
use App\Entity\Product;
use App\Entity\Wallet;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testCreation(): void
    {
        $person = new Person('Alice', 'EUR');
        $this->assertEquals('Alice', $person->getName());
        $this->assertEquals('EUR', $person->getWallet()->getCurrency());
        $this->assertEquals(0, $person->getWallet()->getBalance());
    }

    // Test pour couvrir setName (Code Coverage)
    public function testSetName(): void
    {
        $person = new Person('Alice', 'EUR');
        $person->setName('Bob');
        $this->assertEquals('Bob', $person->getName());
    }

    // Test pour couvrir setWallet (Code Coverage)
    public function testSetWallet(): void
    {
        $person = new Person('Alice', 'EUR');
        $newWallet = new Wallet('USD');
        $person->setWallet($newWallet);
        $this->assertEquals('USD', $person->getWallet()->getCurrency());
    }

    public function testHasFund(): void
    {
        $person = new Person('Alice', 'EUR');
        $person->getWallet()->addFund(10);
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertSimple(): void
    {
        $alice = new Person('Alice', 'EUR');
        $bob = new Person('Bob', 'EUR');

        $alice->getWallet()->addFund(100);
        $alice->transfertFund(50, $bob);

        $this->assertEquals(50, $alice->getWallet()->getBalance());
        $this->assertEquals(50, $bob->getWallet()->getBalance());
    }

    // Réintroduction du test d'erreur (Exception) pour couvrir la ligne "throw new Exception"
    public function testTransfertFundInvalidCurrency(): void
    {
        $alice = new Person('Alice', 'EUR');
        $bob = new Person('Bob', 'USD'); // Devise différente
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        
        $alice->transfertFund(10, $bob);
    }

    public function testDivisionSimple(): void
    {
        $alice = new Person('Alice', 'EUR');
        $alice->getWallet()->addFund(100);

        $bob = new Person('Bob', 'EUR');
        $charlie = new Person('Charlie', 'EUR');

        $alice->divideWallet([$bob, $charlie]);

        $this->assertEquals(0, $alice->getWallet()->getBalance());
        $this->assertEquals(50, $bob->getWallet()->getBalance());
        $this->assertEquals(50, $charlie->getWallet()->getBalance());
    }

    // Test pour vérifier que le filtre de devise dans divideWallet fonctionne
    // (Couvre la fonction fléchée fn($person) => ...)
    public function testDivisionAvecDeviseInvalide(): void
    {
        $alice = new Person('Alice', 'EUR');
        $alice->getWallet()->addFund(100);

        $bob = new Person('Bob', 'EUR');
        $charlie = new Person('Charlie', 'USD'); // Lui il ne doit rien recevoir

        // On divise entre Bob (valide) et Charlie (invalide)
        // Alice donne tout à Bob (100 / 1 = 100)
        $alice->divideWallet([$bob, $charlie]);

        $this->assertEquals(0, $alice->getWallet()->getBalance());
        $this->assertEquals(100, $bob->getWallet()->getBalance());
        $this->assertEquals(0, $charlie->getWallet()->getBalance());
    }

    public function testAchatProduit(): void
    {
        $alice = new Person('Alice', 'USD');
        $alice->getWallet()->addFund(50);

        $product = new Product('Livre', ['USD' => 20], 'other');
        $alice->buyProduct($product);

        $this->assertEquals(30, $alice->getWallet()->getBalance());
    }

    // Réintroduction du test d'erreur pour buyProduct
    public function testAchatProduitDeviseInvalide(): void
    {
        $alice = new Person('Alice', 'USD');
        // Produit dispo seulement en EUR
        $product = new Product('Baguette', ['EUR' => 1], 'food');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');

        $alice->buyProduct($product);
    }
}