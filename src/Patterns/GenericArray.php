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

    static public  function transfoRecursive(& $iterableStuff,$callable)
    {
        foreach ($iterableStuff as $k => $value) {
            if (is_array($value) || is_object($value) ) {
                self::transfoRecursive($value,$callable);
            } else {
                $val = $callable($value) ;
                if (is_array($iterableStuff)) {
                    $iterableStuff[$k] = $val;
                } else {
                    $iterableStuff->{$k} = $callable($value);
                }
            }
        }
    }

}