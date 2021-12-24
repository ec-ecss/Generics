<?php

namespace CaptainKant\Generics\Exceptions;

use Exception;

class GenericPathNotFoundException extends Exception
{
    public function __construct($path)
    {
        parent::__construct("$path not found" );
    }
}