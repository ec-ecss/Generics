<?php

namespace CaptainKant\Generics\Core;

use Exception;

class GenericDecorator
{

    private $realInstance;
    private $wasInvoked = false;

    /**
     * GenericWrapping constructor.
     * @param $realInstance
     */
    public function __construct($realInstance)
    {
        $this->realInstance = $realInstance;
    }

    static public function getDecoratedInstance($i)
    {
        return new GenericDecorator($i);
    }

    static public function redefineClass($file, $newClassName, $oldClassName = '')
    {
        if ('' === $oldClassName) {
            preg_match('#.*/([A-Za-z0-9_]+)\.php#', $file, $tabPreg);
            $oldClassName = $tabPreg[1];
        }
        $contents = file_get_contents($file);
        $contents = str_replace('<?php', '', $contents);
        $contents = str_replace('?>', '', $contents);
        $contents = str_replace($oldClassName, $newClassName, $contents);
        eval($contents);
    }

    public function __call($method, $args)
    {
        if (!$this->wasInvoked && method_exists($this->realInstance, '__invoke')) { //Auto Invoke
            $fct = new \ReflectionMethod($this->realInstance, '__invoke');
            if ($fct->getNumberOfRequiredParameters() === 0) {
                try {
                    call_user_func([$this->realInstance, '__invoke']);
                } catch (Exception $e) {
                    throw new Exception('Generic Decorator of ' . get_class($this->realInstance) . ' failed to __invoke .', 0, $e);
                }
            }
        }
        return call_user_func_array([$this->realInstance, $method], $args);
    }

    public function __invoke()
    {
        $this->wasInvoked = true;
        if (method_exists($this->realInstance, '__invoke')) {
            return call_user_func_array([$this->realInstance, '__invoke'], func_get_args());
        }
        throw new Exception(get_class($this->realInstance) . ' does not have an __invoke method');

    }

}