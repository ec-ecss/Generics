<?php
/** @noinspection ALL */

namespace CaptainKant\Generics\Tests\Cleaners\Resources;

require __DIR__ . '/ClassWithoutNamespace.php';

use CaptainKant\Generics\Core\GenericClassPropertyType;
use CaptainKant\Generics\Tests\Cleaners\Resources\ClassTypageExample as Pippin;
use ClassWithoutNamespace;


class ClassTypageExample
{
    /**
     * @var Pippin
     */
    private $testAlias;
    /**
     * @var CaptainKant\Generics\Tests\Cleaners\Resources\ClassTypageExample
     */
    private $testFullRootname;

    /**
     * @var GenericClassPropertyType
     */
    private $testRelativeUse;

    /**
     * @var GenericClassPropertyType[]
     */
    private $testRelativeUseArray;

    /**
     * @var ClassTypageExample2
     */
    private $hereNameSpace;

    /**
     * @var ClassWithoutNamespace
     */
    private $noNameSpaceWithUse;

    /**
     * @var \ClassWithoutNamespace
     */
    private $noNameWithBackslash;

    /**
     * @var string
     */
    private $aString;


    /**
     * @var self
     */
    private $me1;

    /**
     * @var static
     */
    private $me2;


}