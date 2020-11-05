<?php

namespace CaptainKant\Generics\Exceptions;

use Exception;
use Throwable;

class GenericXmlStringHandlerException extends Exception
{

    /**
     * XmlHandlerException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Xml Parser Fatal Error', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}