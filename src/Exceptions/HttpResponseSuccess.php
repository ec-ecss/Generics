<?php

namespace CaptainKant\Generics\Exceptions;

class HttpResponseSuccess extends HttpResponseException
{
    public function __construct($message = 'Ok')
    {
        parent::__construct($message, 200);
    }
}