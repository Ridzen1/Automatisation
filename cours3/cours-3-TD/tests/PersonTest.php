<?php

namespace Tests;

use App\Entity\Person;
use App\Entity\Product;
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

    public function testAchatProduit(): void
    {
        $alice = new Person('Alice', 'USD');
        $alice->getWallet()->addFund(50);

        $product = new Product('Livre', ['USD' => 20], 'other');
        $alice->buyProduct($product);

        $this->assertEquals(30, $alice->getWallet()->getBalance());
    }
}
