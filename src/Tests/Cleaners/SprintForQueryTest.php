<?php

namespace CaptainKant\Generics\Tests\Cleaners;

use CaptainKant\Generics\Core\GenericMysqlConnexion;
use PHPUnit\Framework\TestCase;

class SprintForQueryTest extends TestCase
{
    public function test_sprint()
    {
        /**
         * On a besoin d'une connexion MySQL si le test unitaire plante vérifier le paramétrage
         */

        $str = "Un soleil rouge se lève, beaucoup de sang a du couler cette nuit";
        $strQuery = "Un soleil %s se lève, beaucoup de sang a du couler cette %s";
        $this->assertEquals(
            $str,
            GenericMysqlConnexion::getInstance()->sprintfForQuery($strQuery, ['rouge', 'nuit'])
        );

    }

}