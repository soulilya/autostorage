<?php

namespace App\Cars;

class CarFactory
{
    /**
     * @return Car
     */
    public function createCar(): Car
    {
        return new Car();
    }
}