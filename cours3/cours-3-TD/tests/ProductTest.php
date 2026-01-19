<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCreation(): void
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $this -> assertEquals('eau', $product-> getName());
        $this -> assertEqualsCanonicalizing([9.99, 14.99], $product-> getPrices());
        $this -> assertEquals('other', $product-> getType());
    }

    public function testProductSetTypeGood():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $product -> setType('food');
        $this -> assertEquals('food', $product-> getType());
    }

    public function testProductSetTypeInvalid():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $this->expectException(\Exception::class);
        $product -> setType('invalid');
    }

    public function testProductSetPricesGood():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $product -> setPrices(['EUR' => 5.99,'USD' => 24.99]);
        $this -> assertEquals(['EUR' => 5.99,'USD' => 24.99], $product-> getPrices());
    }

    public function testProductSetPricesInvalid():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $product -> setPrices(['ABC' => 5.99,'DEF' => 24.99]);
        $this -> assertEqualsCanonicalizing([9.99, 14.99], $product-> getPrices());
    }

    public function testProductSetPricesNegativeValues():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $product -> setPrices(['EUR' => -5.99,'USD' => -24.99]);
        $this -> assertEquals(['EUR' => 9.99,'USD' => 14.99], $product-> getPrices());
    }

    public function testProductSetName():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $product -> setName('water');
        $this -> assertEquals('water', $product-> getName());
    }

    public function testProductGetTVA():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $this -> assertEquals(0.2, $product-> getTVA());
    }

    public function testProductListCurrencies():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $this -> assertEquals(['EUR','USD'], $product-> listCurrencies());
    }

    public function testProductGetPriceGood():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $this -> assertEquals(9.99, $product-> getPrice('EUR'));
    }

    public function testProductGetPriceWrongCurrency():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99,'USD' => 14.99],'other');
        $this->expectException(\Exception::class);
        $product-> getPrice('RSB');
    }

    public function testProductGetPriceCurrencyNotAvailable():void 
    {
        $product = new \App\Entity\Product('eau',['EUR' => 9.99],'other');
        $this->expectException(\Exception::class);
        $product-> getPrice('USD');
    }
}