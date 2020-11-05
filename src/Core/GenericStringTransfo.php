<?php


namespace CaptainKant\Generics\Core;


class GenericStringTransfo
{

    static public function camelCaseToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}