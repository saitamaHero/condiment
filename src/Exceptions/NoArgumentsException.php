<?php

namespace Condiment\Exceptions;

class NoArgumentsException extends \Exception
{
   
    public function __construct()
    {
        parent::__construct("No Arguments was provided", 101);
    }
}
