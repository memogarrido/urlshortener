<?php

/**
 * Class used to get a String hash from an integer
 */
class IntegerHash {

    const ALPHABET = 'D1LEpr9OlkiTsxR3XNmWAbChPBHnfJSMFQjVa5cGKuU0dY2z6gyZIweqv74to8';
    const BASE = 62;

    public static function encode($num) {
        $str = '';
        while ($num > 0) {
            $str = self::ALPHABET[($num % self::BASE)] . $str;
            $num = (int) ($num / self::BASE);
        }
        return $str;
    }

    public static function decode($str) {
        $num = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * self::BASE + strpos(self::ALPHABET, $str[$i]);
        }
        return $num;
    }

}
