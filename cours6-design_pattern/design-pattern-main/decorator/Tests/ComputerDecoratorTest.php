<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use App\Laptop;
use App\GPUDecorator;
use App\OLEDScreenDecorator;

class ComputerDecoratorTest extends TestCase
{
    public function testBasicLaptop()
    {
        $laptop = new Laptop();
        
        $this->assertSame(400, $laptop->getPrice());
        $this->assertSame("A laptop computer", $laptop->getDescription());
    }

    public function testLaptopWithGPU()
    {
        $laptop = new Laptop();
        $laptopWithGPU = new GPUDecorator($laptop);
        $this->assertSame(650, $laptopWithGPU->getPrice());
        $this->assertSame("A laptop computer, with a GPU", $laptopWithGPU->getDescription());
    }

    public function testLaptopWithOLEDScreen()
    {
        $laptop = new Laptop();
        $laptopWithOLED = new OLEDScreenDecorator($laptop);
        
        $this->assertSame(550, $laptopWithOLED->getPrice());
        $this->assertSame("A laptop computer, with an OLED screen", $laptopWithOLED->getDescription());
    }
    
    public function testLaptopWithGPUAndOLED()
    {
        $laptop = new Laptop();
        $laptopWithGPU = new GPUDecorator($laptop);
        $fullyLoadedLaptop = new OLEDScreenDecorator($laptopWithGPU);

        $this->assertSame(800, $fullyLoadedLaptop->getPrice());
        $this->assertSame("A laptop computer, with a GPU, with an OLED screen", $fullyLoadedLaptop->getDescription());
    }
}