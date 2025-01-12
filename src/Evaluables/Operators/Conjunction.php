<?php 

namespace Condiment\Evaluables\Operators;


final class Conjunction extends CompoundStatement
{
    protected function compare(bool $a, \Closure $next): bool
    {
        return $a && $next();
    }
}
