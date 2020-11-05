<?php


namespace CaptainKant\Generics\Interfaces;


interface GenericBinderServiceInterface
{

    public function bind(string $strclass1, string $strclass2);


    public function binded(string $strclass1): string;
}