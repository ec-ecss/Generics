<?php

namespace CaptainKant\Generics\Tests\Cleaners;

use CaptainKant\Generics\Core\GenericClassPropertyType;
use CaptainKant\Generics\Tests\Cleaners\Resources\ClassTypageExample;
use CaptainKant\Generics\Tests\Cleaners\Resources\ClassTypageExample2;
use CaptainKant\Generics\Tests\Cleaners\Resources\ClassTypageExample74;
use ClassWithoutNamespace;
use PHPUnit\Framework\TestCase;
use ReflectionClass;


class GenericClassPropertyTypeTest extends TestCase
{

    public function testFullNamespaceInDoc()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassTypageExample::class, $typer($reflexion->getProperty('testFullRootname')));
    }

    /**
     * @return array
     */
    private function typer(): array
    {
        $reflexion = new ReflectionClass(ClassTypageExample::class);
        $typer = new GenericClassPropertyType();
        return array($reflexion, $typer);
    }

    public function testUsedNamespaceInDoc()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(GenericClassPropertyType::class, $typer($reflexion->getProperty('testRelativeUse')));
    }

    public function testUsedNamespaceInDocArray()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(GenericClassPropertyType::class . '[]', $typer($reflexion->getProperty('testRelativeUseArray')));
    }

    public function testAliasInDoc()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassTypageExample::class, $typer($reflexion->getProperty('testAlias')));
    }

    public function testHereNameSpace()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassTypageExample2::class, $typer($reflexion->getProperty('hereNameSpace')));
    }

    public function testNoNamespace1()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassWithoutNamespace::class, $typer($reflexion->getProperty('noNameSpaceWithUse')));
    }

    public function testNoNamespace2()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassWithoutNamespace::class, $typer($reflexion->getProperty('noNameWithBackslash')));
    }

    public function testString()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals('string', $typer($reflexion->getProperty('aString')));
    }

    public function testMe()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassTypageExample::class, $typer($reflexion->getProperty('me1')));

        $this->assertEquals(ClassTypageExample::class, $typer($reflexion->getProperty('me2')));
    }

    /**
     * @requires PHP 7.4
     */
    public function test74()
    {
        list($reflexion, $typer) = $this->typer();
        $this->assertEquals(ClassTypageExample74::class, $typer($reflexion->getProperty('testAlias')));
    }


}