<?php


namespace CaptainKant\Generics\Cleaners;


class GenericEncodingCleaner
{
    static public function assumeUTF8(&$str)
    {
        if (!self::isUTF8($str)) {
            $str = utf8_encode($str);
        }
    }

    static public function isUTF8($str)
    {
        if (mb_detect_encoding($str . 'bugfix', 'UTF-8, ISO-8859-1') != 'UTF-8')
            return utf8_encode($str);
        return $str;
    }

    public function autoclean(&$str)
    {

        if (false !== strpos($str, 'encoding="UTF-8"') && !self::isUTF8($str)) {
            $str = utf8_encode($str);
        }
    }
}