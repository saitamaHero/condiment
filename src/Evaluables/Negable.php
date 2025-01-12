<?php 

namespace Condiment\Evaluables;

interface Negable
{
    public function negate(): \Condiment\Evaluables\Operators\Negation;
}

