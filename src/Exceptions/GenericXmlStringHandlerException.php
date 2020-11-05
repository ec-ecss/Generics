<?php

namespace CaptainKant\Generics\Exceptions;

use Exception;
use LibXMLError;
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

    public function getStrProblems()
    {
        implode(', ', array_map(function (LibXMLError $error) {
            return $error->message;
        }, libxml_get_errors()));
    }
}