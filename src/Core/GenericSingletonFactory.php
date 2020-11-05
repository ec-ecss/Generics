<?php


namespace CaptainKant\Generics\Core;


use CaptainKant\Generics\Interfaces\GenericAutowiringServiceInterface;
use CaptainKant\Generics\Traits\GenericAutowiringServiceTrait;

class GenericSingletonFactory implements GenericAutowiringServiceInterface
{
    use GenericAutowiringServiceTrait;

    private $singletons;

    public function getOrSetSingleton(string $uuidSingleton, callable $callableForCreation)
    {
        return $this->singletons[$uuidSingleton] ?? ($this->singletons[$uuidSingleton] = $callableForCreation());
    }

    public function getSingleton(string $uuidSingleton)
    {
        return $this->singletons[$uuidSingleton] ?? null;
    }
}