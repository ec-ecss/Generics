<?php

namespace CaptainKant\Generics\Patterns;

use CaptainKant\Generics\Exceptions\GenericResolutionException;
use PHPUnit\Framework\TestCase;

class GenericDependancyResolutionTest extends TestCase
{

    public function testResolveDependancies()
    {

        list($gandalf, $gollum, $theOneRing, $sam, $frodon, $sauron, $fish, $weed, $destroyTheRing) =
            $this->createElements(['Gandalf', 'Gollum', 'The One Ring', 'Sam', 'Frodon', 'Sauron', 'Fish', 'Weed', 'Destroy The Ring']);

        $dependancyResolver = new GenericDependancyResolution();
        $dependancyResolver->registerDependancy($gandalf, $weed);
        $dependancyResolver->registerDependancy($sam, $weed);
        $dependancyResolver->registerDependancy($frodon, $weed);
        $dependancyResolver->registerDependancy($sauron, $theOneRing);
        $dependancyResolver->registerDependancy($gollum, $theOneRing);
        $dependancyResolver->registerDependancy($frodon, $theOneRing);
        $dependancyResolver->registerDependancy($gollum, $fish);
        $dependancyResolver->registerDependancy($sam, $frodon);
        $dependancyResolver->registerDependancy($frodon, $destroyTheRing);
        $dependancyResolver->registerDependancy($gandalf, $destroyTheRing);
        $dependancyResolver->registerDependancy($destroyTheRing, $theOneRing);

        $this->assertEquals(
            ['Weed', 'The One Ring', 'Destroy The Ring', 'Gandalf', 'Frodon', 'Sam', 'Sauron', 'Fish', 'Gollum'],
            $this->mapToString($dependancyResolver->getResolvedListDepandancies()));

        $dependancyResolver->registerDependancy($theOneRing, $sauron);

        $this->expectException(GenericResolutionException::class);

        $dependancyResolver->getResolvedListDepandancies();

    }

    private function createElements($tabNameEles)
    {
        $element = new class($name = '') {
            private $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }

            public function __toString(): string
            {
                return $this->name;
            }
        };
        $tabRes = [];
        foreach ($tabNameEles as $nameEle) {
            $tabRes[] = new $element($nameEle);
        }
        return $tabRes;
    }

    private function mapToString($arr)
    {
        $res = [];
        foreach ($arr as $val) {
            $res[] = (string)$val;
        }
        return $res;
    }

    public function testResolveString()
    {
        $dependancyResolver = new GenericDependancyResolution();
        $dependancyResolver->registerDependancy('saroumane', 'orcs');
        $dependancyResolver->registerDependancy('saroumane', 'baton');
        $dependancyResolver->registerDependancy('orcs', 'killing');
        $dependancyResolver->registerDependancy('sauron', 'saroumane');
        $this->assertEquals(
            ['killing', 'orcs', 'baton', 'saroumane', 'sauron'],
            $this->mapToString($dependancyResolver->getResolvedListDepandancies()));
    }
}
