<?php


namespace CaptainKant\Generics\Core;


use CaptainKant\Generics\Interfaces\GenericAutowiringServiceInterface;
use CaptainKant\Generics\Traits\GenericAutowiringServiceTrait;

abstract class GenericAutowiringService implements GenericAutowiringServiceInterface
{
    use GenericAutowiringServiceTrait;
}