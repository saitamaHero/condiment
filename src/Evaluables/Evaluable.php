<?php 

namespace Condiment\Evaluables;

/**
 * [Description Evaluable]
 */
interface  Evaluable
{
    /**
     * @return bool
     */
    public function evaluate(): bool;
}
