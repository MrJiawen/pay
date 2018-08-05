<?php

namespace Jw\Pay\Library;

class ArrayTool
{
    /**
     * Convert encoding.
     * @param $array
     * @param $to_encoding
     * @param string $from_encoding
     * @return array
     * @Author jiaWen.chen
     */
    public static function encoding($array, $to_encoding, $from_encoding = 'gb2312')
    {
        $encoded = [];

        foreach ($array as $key => $value) {
            $encoded[$key] = is_array($value) ? self::encoding($value, $to_encoding, $from_encoding) :
                mb_convert_encoding($value, $to_encoding, $from_encoding);
        }

        return $encoded;
    }
}