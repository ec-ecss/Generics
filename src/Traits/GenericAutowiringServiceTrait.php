<?php

namespace CaptainKant\Generics\Traits;

use CaptainKant\Generics\Core\GenericBinder;
use CaptainKant\Generics\Core\GenericClassPropertyType;
use CaptainKant\Generics\Core\GenericDecorator;
use CaptainKant\Generics\Exceptions\GenericClassPropertyTypeException;
use CaptainKant\Generics\Interfaces\GenericAutowiringServiceInterface;
use ReflectionClass;
use ReflectionProperty;

trait GenericAutowiringServiceTrait
{

    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @var self
     */
    private static $realInstance = null;

    /**
     * @return static
     */
    static public function getInstance()
    {
        if (null === self::$instance ?? null) {
            return self::createInstance();
        }
        return self::$instance;
    }

    /**
     * @return static
     */
    static private function createInstance()
    {
        if (static::class !== GenericBinder::class) {
            $bindedClassName = GenericBinder::getInstance()->binded(static::class);
            self::$realInstance = new $bindedClassName();
        } else {
            self::$realInstance = new static();
        }

        self::$instance = GenericDecorator::getDecoratedInstance(self::$realInstance);
        self::$realInstance->autoWire();
        return self::$instance;
    }

    public function autoWire()
    {

        $reflexion = new ReflectionClass($this);
        $bindingService = GenericBinder::getInstance();

        foreach ($reflexion->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED) as $property) {

            try {
                $type = GenericClassPropertyType::unitOfWork($property);
            } catch (GenericClassPropertyTypeException $e) {
                continue;
            }
            $type = $bindingService->binded($type);
            if (null !== $type && class_exists($type)) {
                $classImplements = GenericClassPropertyType::doesClassImplements($type, GenericAutowiringServiceInterface::class);
                if (defined('CPK_GENERIC_AUTOWIRING_DONT_CHECK_INTERFACE')) {
                    $classImplements = method_exists($type, 'getInstance');
                }
                if (!$classImplements) {
                    continue;
                }
                $property->setAccessible(true);
                $property->setValue($this, call_user_func($type . '::getInstance'));
            }
        }

    }

    /**
     * @return static
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpDocSignatureInspection
     */
    static public function newInstance()
    {
        $instance = new static();
        $decoratedInstance = GenericDecorator::getDecoratedInstance($instance);
        $instance->autoWire();
        return $decoratedInstance;
    }

    /**
     * For autocompletion purposes
     * @param $a
     * @return static
     */
    static public function a($a)
    {
        return $a;
    }

    public function load()
    {
        $this->__invoke();
    }

}