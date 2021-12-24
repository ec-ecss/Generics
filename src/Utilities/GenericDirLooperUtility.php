<?php

namespace CaptainKant\Generics\Utilities;

use CaptainKant\Generics\Core\GenericFile;
use CaptainKant\Generics\Exceptions\GenericPathNotFoundException;

/**
 * @noinspection PhpUnused
 */
class GenericDirLooperUtility
{

    /**
     * @noinspection PhpUnused
     * @throws GenericPathNotFoundException
     */
    static function loopOnDir($pathDir, $callable){
        $dh = opendir($pathDir);
        if ($dh === false) {
            throw new GenericPathNotFoundException($pathDir);
        }
        while (($basenameFile = readdir($dh)) !== false) {
            $file = new GenericFile($pathDir.'/'.$basenameFile);
            $callable($file);
        }
    }

}