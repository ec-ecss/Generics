<?php

namespace CaptainKant\Generics\Exceptions;

use Exception;

/**
 * TODO : est-ce psr-15 ? https://www.php-fig.org/psr/psr-15/
 * Class HttpResponseException
 * @package CaptainKant\Generics\Exceptions
 */
class HttpResponseException extends Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }

    public function printHeaderAndDie()
    {
        header("HTTP/1.0 {$this->getCode()} {$this->getMessage()}");
        die;
    }
}