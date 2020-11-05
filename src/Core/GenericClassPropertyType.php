<?php


namespace CaptainKant\Generics\Core;


use CaptainKant\Generics\Exceptions\GenericClassPropertyTypeException;
use ReflectionProperty;

class GenericClassPropertyType
{

    static public function extractLastName(string $namespace): string
    {
        return false !== ($i = strrpos($namespace, '\\')) ? substr($namespace, $i + 1) : $namespace;
    }

    static public function doesClassImplements($classname, $interfacename)
    {
        $interfaces = class_implements($classname);
        if (isset($interfaces[$interfacename])) {
            return true;
        }
        return false;
    }

    /**
     * @param ReflectionProperty $property
     * @return string
     * @throws GenericClassPropertyTypeException
     */
    public function __invoke(ReflectionProperty $property)
    {
        return self::unitOfWork($property);
    }

    /**
     * @param ReflectionProperty $property
     * @return string
     * @throws GenericClassPropertyTypeException
     */
    static public function unitOfWork(ReflectionProperty $property)
    {
        $type = null;
        if (method_exists($property, 'getType')) {
            /**
             * Get Type of property PHP >= 7.4
             */
            /** @noinspection ALL */
            return (string)$property->getType();
        }

        /**
         * Get Type of property PHP < 7.4. Fly, you fools.
         */
        if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
            list(, $type) = $matches;
        } else {
            throw new GenericClassPropertyTypeException('No type for property ' . $property->name . ' on class ' . $property->getDeclaringClass()->name);
        }

        if ('self' === $type || 'static' === $type) {
            return $property->getDeclaringClass()->name;
        }

        //Is it a array ?
        $isArray = false;
        if (false !== ($baseType = strstr($type, '[]', true))) {
            $type = $baseType;
            $isArray = true;
        }

        //Root namespace
        if ('\\' == substr($type, 0, 1)) {
            $type = substr($type, 1);
            return $type . ($isArray ? '[]' : '');
        }

        //already a namespace
        if (false !== strpos($type, '\\')) {
            return $type . ($isArray ? '[]' : '');
        }

        //PHP Base type
        if (in_array($type, self::typesOfBase())) {
            return $type . ($isArray ? '[]' : '');
        }


        $strPhpClassDefinition = file_get_contents($property->getDeclaringClass()->getFileName());

        /**
         * relative namespace without alias
         */
        if (preg_match("#\R[ \t]*use[ ]+([\\\A-Za-z0-9_]*){$type}[ \t]*;[ \t]*\R#", $strPhpClassDefinition, $matches)) {
            $prefix = rtrim($matches[1], '\\');
            return ('' !== $prefix ? $prefix . '\\' : '') . $type . ($isArray ? '[]' : '');
        }

        /**
         * with an alias
         */
        if (preg_match("#\R[ \t]*use[ ]+([\\\A-Za-z0-9_]+)[ \t]*[ \t]*as[ \t]*{$type}[ \t]*;[ \t]*\R#", $strPhpClassDefinition, $matches)) {
            return $matches[1] . ($isArray ? '[]' : '');
        }

        /**
         * extract current namespace
         */
        if (preg_match("#\R[ \t]*namespace[ ]+([\\\A-Za-z0-9_]+)[ \t]*;[ \t]*\R#", $strPhpClassDefinition, $matches)) {
            $currentNamespace = $matches[1];
            return $currentNamespace . '\\' . $type . ($isArray ? '[]' : '');
        }


        return $type;


    }

    /**
     * @return string[]
     */
    static public function typesOfBase()
    {
        return ['boolean', 'bool', 'int', 'integer', 'string', 'double', 'float'];
    }

}