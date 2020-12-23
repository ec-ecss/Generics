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

    static public function transfoRecursive(&$iterableStuff, $callable)
    {
        $bagOfHashs = [];
        self::transfoRecursive_op($iterableStuff, $callable, $bagOfHashs);
    }

    static private function transfoRecursive_op(&$iterableStuff, $callable, &$bagOfHashs)
    {
        if (is_object($iterableStuff)) {
            $bagOfHashs[] = spl_object_hash($iterableStuff);
        }
        foreach ($iterableStuff as $k => $value) {
            if (is_array($value) || is_object($value)) {
                if (is_object($value)) {
                    if (in_array(spl_object_hash($value), $bagOfHashs)) {
                        continue;
                    }
                }
                self::transfoRecursive_op($value, $callable, $bagOfHashs);
            } else {
                $val = $callable($value);
                if (is_array($iterableStuff)) {
                    $iterableStuff[$k] = $val;
                } else {
                    $iterableStuff->{$k} = $callable($value);
                }
            }
        }
    }

}