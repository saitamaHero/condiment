<?php 

namespace Condiment\Evaluables\Operators;

use Condiment\Evaluables;

final class Negation extends LogicalOperator
{
    public function evaluate(): bool
    {
        if ($this->evaluablesCount !== 1) {
            //TODO needs to throw an Exception
            throw new \Exception("Error you modofoca");
        }

        return !current($this->evaluables)->evaluate();
    }

    public function display(): string
    {
        $result = $this->evaluate();
        $color = $result ? "#007f5f55" : "#880d1e55";

        return "<span style=\"display: block; margin: 1em 0; padding: .15em .5em; border: 1px solid #000; border-radius: 1em; background: $color; \">!(" . strval($this->evaluables[0]) . ")" .
            "</span>";
    }
}