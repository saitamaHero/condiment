<?php 

namespace Condiment\Evaluables\Operators;

/**
 * [Description Disjunction]
 */
final class Disjunction extends CompoundStatement
{
    protected function compare(bool $a, \Closure $next): bool
    {
        return $a || $next();
    }
}