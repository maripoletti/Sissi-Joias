<?php

declare(strict_types= 1);

class utf8ize {
    public static function utf8($word) {
        if (is_array($word)) {
            foreach ($word as $k => $v) {
                $word[$k] = self::utf8($v);
            }
        } else if (is_string($word)) {
            return mb_convert_encoding($word, 'UTF-8', 'UTF-8');
        }
        return $word;
    }
}