<?php


namespace CaptainKant\Generics\Tests;


use CaptainKant\Generics\Patterns\GenericArray;
use PHPUnit\Framework\TestCase;

class GenericArrayTest extends TestCase
{
    public function testTransfoRecursive()
    {
        $ob = new class {
            public $a = 'gollum';
            public $b = null;
        };


        $a = [
            1 => "gandalf",
            'two' => 'frodo',
            3 => $ob
        ];

        GenericArray::transfoRecursive($a, function ($str) {
            return strtoupper($str);
        });

        $this->assertEquals('FRODO', $a['two']);

        $this->assertEquals('GOLLUM', $a[3]->a);

        $ob->b = $ob;
        GenericArray::transfoRecursive($a, function ($str) {
            return strtoupper($str);
        });

        $this->assertEquals('GOLLUM', $a[3]->a);


    }
}