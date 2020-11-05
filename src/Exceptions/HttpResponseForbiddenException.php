<?php

namespace CaptainKant\Generics\Exceptions;

class HttpResponseForbiddenException extends HttpResponseException
{
    public function __construct($message = 'Forbidden')
    {
        parent::__construct($message, 403);
    }
}