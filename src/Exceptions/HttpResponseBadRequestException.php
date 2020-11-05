<?php

namespace CaptainKant\Generics\Exceptions;

class HttpResponseBadRequestException extends HttpResponseException
{
    public function __construct($message = 'Misformed data')
    {
        parent::__construct($message, 400);
    }
}