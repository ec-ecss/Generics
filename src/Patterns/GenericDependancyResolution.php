<?php


namespace CaptainKant\Generics\Patterns;


use CaptainKant\Generics\Exceptions\GenericResolutionException;
use CaptainKant\Generics\Interfaces\GenericStringToObjectInterface;

class GenericDependancyResolution
{
    private $dictionnary = [];
    private $tabWhereIndexIsNeedByValues = [];
    private $tabWhereIndexNeedValues = [];
    private $tabResolution = [];
    private $tabGettingResolved = [];
    private $tabStringsToObject;

    public function registerDependancy($aThingThatNeeds, $thisThing)
    {
        $UIDaThingThatNeeds = $this->register($aThingThatNeeds);
        $UIDthisThing = $this->register($thisThing);
        GenericArray::pushValueToIndex($this->tabWhereIndexIsNeedByValues, $UIDthisThing, $UIDaThingThatNeeds);
        GenericArray::pushValueToIndex($this->tabWhereIndexNeedValues, $UIDaThingThatNeeds, $UIDthisThing);
    }

    public function register($aThing)
    {
        if (is_string($aThing)) {
            $aThing = $this->getObjectFromString($aThing);
        }
        return $this->addDictionary($aThing);
    }

    private function getObjectFromString($string)
    {
        if (!isset($this->tabStringsToObject[$string])) {
            $c = new class() implements GenericStringToObjectInterface {
                public $name;
            };
            $c->name = $string;
            $this->tabStringsToObject[$string] = $c;
        }
        return $this->tabStringsToObject[$string];
    }

    private function addDictionary($stuff): string
    {
        $spl_object_hash = spl_object_hash($stuff);
        $this->dictionnary[$spl_object_hash] = $stuff;
        return $spl_object_hash;
    }

    public function getResolvedListDepandancies()
    {
        $this->tabResolution = [];
        $this->tabGettingResolved = [];
        foreach ($this->dictionnary as $idElement => $element) {
            $this->doNecessaryStuffAndResolve($idElement);
        }
        if (end($this->tabResolution) instanceof GenericStringToObjectInterface) { //faked strings to objects
            return array_map(function ($a) {
                return $a->name;
            }, $this->tabResolution);
        }
        return $this->tabResolution;
    }

    private function doNecessaryStuffAndResolve($idStuff)
    {
        $this->resolveDependanciesFor($idStuff);
        $this->actResolutionIfNecessary($idStuff);
    }

    /**
     * @param $idStuff
     * @throws GenericResolutionException
     */
    private function resolveDependanciesFor($idStuff)
    {

        if (!isset($this->tabWhereIndexNeedValues[$idStuff])) {
            return;
        }
        $this->tabGettingResolved[] = $idStuff;
        foreach ($this->tabWhereIndexNeedValues[$idStuff] as $idThatStuffNeeds) {
            if (isset($this->tabResolution[$idThatStuffNeeds]) || $idThatStuffNeeds === $idStuff) {
                continue;
            }

            if (in_array($idThatStuffNeeds, $this->tabGettingResolved)) {
                throw new GenericResolutionException("Cyclic Resolution Trap Detected");
            }

            $this->doNecessaryStuffAndResolve($idThatStuffNeeds);
        }
    }

    private function actResolutionIfNecessary($idStuff)
    {
        if (isset($this->tabResolution[$idStuff])) {
            return;
        }
        $this->tabResolution[$idStuff] = $this->dictionnary[$idStuff];
    }


}