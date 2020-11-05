<?php


namespace CaptainKant\Generics\Patterns;

class GenericArray
{
    static public function pushValueToIndex(&$array, $index, $val)
    {
        if (!isset($array[$index])) {
            $array[$index] = [];
        }
        $array[$index][] = $val;
    }

}