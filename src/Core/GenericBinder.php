<?php


namespace CaptainKant\Generics\Core;


use CaptainKant\Generics\Interfaces\GenericAutowiringServiceInterface;
use CaptainKant\Generics\Interfaces\GenericBinderServiceInterface;
use CaptainKant\Generics\Traits\GenericAutowiringServiceTrait;

class GenericBinder implements GenericBinderServiceInterface, GenericAutowiringServiceInterface
{
    use GenericAutowiringServiceTrait;

    private $binds = [];

    public function bind(string $classNameOri, string $classNameFinal)
    {
        $this->binds[$classNameOri] = $classNameFinal;
    }

    public function binded(string $classNameOri): string
    {
        return $this->binds[$classNameOri] ?? $classNameOri;
    }

}