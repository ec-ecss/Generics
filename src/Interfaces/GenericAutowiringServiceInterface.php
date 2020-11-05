<?php


namespace CaptainKant\Generics\Interfaces;

interface GenericAutowiringServiceInterface
{
    /**
     * @return static
     */
    public static function getInstance();
}