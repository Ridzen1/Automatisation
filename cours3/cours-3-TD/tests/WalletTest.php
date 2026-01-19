<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;

class WalletTest extends TestCase
{
    public function testWalletCreation(): void
    {
        $wallet = new Wallet('USD');
        $this->assertEquals(0, $wallet->getBalance());
        $this->assertEquals('USD', $wallet->getCurrency());
    }

    public function testSetBalanceGood(): void 
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(2);
        $this->assertEquals(2, $wallet->getBalance()); 
    }
    
    public function testSetBalanceBad(): void 
    {
        $wallet = new Wallet('USD');
        $this->expectException(\Exception::class);
        $wallet->setBalance(-2);
    }

    public function testRemoveFundGood(): void 
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(2);
        $wallet->removeFund(1);
        $this->assertEquals(1, $wallet->getBalance()); 
    }

    public function testRemoveFundTooMuch(): void 
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(2);
        $this->expectException(\Exception::class);
        $wallet->removeFund(5);
    }

    public function testRemoveFundNegativeValue(): void 
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(2);
        $this->expectException(\Exception::class);
        $wallet->removeFund(-5);
    }

    public function testAddFundGood(): void 
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(2);
        $wallet->addFund(3);
        $this->assertEquals(5, $wallet->getBalance()); 
    }

    public function testAddFundNegativeValue(): void 
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(2);
        $this->expectException(\Exception::class);
        $wallet->addFund(-3);
    }

    // --- NOUVEAUX TESTS AJOUTÉS POUR LE 100% ---

    public function testSetCurrencyGood(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setCurrency('EUR');
        $this->assertEquals('EUR', $wallet->getCurrency());
    }

    public function testSetCurrencyBad(): void
    {
        $wallet = new Wallet('USD');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        $wallet->setCurrency('JPY'); // Devise invalide
    }
}