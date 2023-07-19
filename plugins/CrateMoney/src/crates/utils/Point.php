<?php

namespace crates\utils;

class Point
{
    private float $x;
    private float $z;

    public function __construct(float $x, float $z)
    {
        $this->x = $x;
        $this->z = $z;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getZ(): float
    {
        return $this->z;
    }
}