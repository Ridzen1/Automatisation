<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{

    public function testWalletCreation(): void
    {
        $wallet = new \App\Entity\Wallet('USD');
        $this->assertEquals(0, $wallet->getBalance());
        $this->assertEquals('USD', $wallet->getCurrency());
    }

    public function testSetBalanceGood():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $wallet -> setbalance(2);
        $this -> assertEquals(2, $wallet->getBalance()); 
    }
    
    public function testSetBalanceBad():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $this->expectException(\Exception::class);
        $wallet -> setbalance(-2);
    }

    public function testRemoveFundGood():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $wallet -> setbalance(2);
        $wallet -> removeFund(1);
        $this -> assertEquals(1, $wallet->getBalance()); 
    }

    public function testRemoveFundTooMuch():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $wallet -> setbalance(2);
        $this->expectException(\Exception::class);
        $wallet -> removeFund(5);
    }

    public function testRemoveFundNegativeValue():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $wallet -> setbalance(2);
        $this->expectException(\Exception::class);
        $wallet -> removeFund(-5);
    }

    public function testAddFundGood():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $wallet -> setbalance(2);
        $wallet -> addFund(3);
        $this -> assertEquals(5, $wallet->getBalance()); 
    }

        public function testAddFundNegativeValue():void 
    {
        $wallet = new \App\Entity\Wallet('USD');
        $wallet -> setbalance(2);
        $this->expectException(\Exception::class);
        $wallet -> addFund(-3);
    }
}
