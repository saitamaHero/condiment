<?php

namespace Condiment\Exceptions;

class NoArgumentsException extends \Exception
{

    public function __construct(string $class = "")
    {
        parent::__construct("No arguments provided to " . $class . " condition.", 101);
    }
}
