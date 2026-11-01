<?php

namespace App\Services;

class Calculator
{
    public function add($x, $y)
    {
        return $x+$y; // порушення стандарту форматування
    }

    public function subtract($x, $y)
    {
        return $x - $y;
    }
}