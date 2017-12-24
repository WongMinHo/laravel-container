<?php

/**
 * User: MinHow
 * Date: 2017/12/2
 */
//驾驶员
class Driver
{
    protected $car;
    public function __construct(Car $car)
    {
        $this->car = $car;
    }
    public function driveCar()
    {
        $this->car->drive();
    }
}