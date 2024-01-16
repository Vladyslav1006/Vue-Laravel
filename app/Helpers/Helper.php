<?php

namespace App\Helpers;

class Helper
{
    public static function NextMBJ($ex_mbj = '')
    {
        $array = preg_split("/[0-9]+/", $ex_mbj);
        preg_match('!\d+!', $ex_mbj, $array2);
        $p1 = $array[0] ? $array[0] : 'MBJ';
        $p2 = isset($array2[0]) && $array2[0] + 1 > 999 ? 1 : @$array2[0] + 1;
        $p3 = $array[1] ?? '';
        if($p2 == 1) {
            $p3 = empty($array[1]) ? ($array[0] ? 'A' : '') : ++$array[1];
        }

        return  $p1 . $p2 . $p3;
    }

    public static function NextCN($ex_mbj = '')
    {
        $array = preg_split("/[0-9]+/", $ex_mbj);
        preg_match('!\d+!', $ex_mbj, $array2);
        $p1 = $array[0] ? (isset($array2[0]) && $array2[0] + 1 > 9999 ? ++$array[0] : $array[0]) : 'B';
        $p2 = isset($array2[0]) && $array2[0] + 1 > 9999 ? 1 : @$array2[0] + 1;

        return   strtolower($p1) . sprintf('%04d', $p2);
    }
    public static function NextInv($ex_mbj = '')
    {
        $array = preg_split("/[0-9]+/", $ex_mbj);
        preg_match('!\d+!', $ex_mbj, $array2);
        $p1 = $array[1] ? (isset($array2[0]) && $array2[0] + 1 > 9999 ? ++$array[1] : $array[1]) : 'B';
        $p2 = isset($array2[0]) && $array2[0] + 1 > 9999 ? 1 : @$array2[0] + 1;

        return     sprintf('%04d', $p2) . $p1;
    }

    public static function NextColKey($key = '')
    {

        if($key == '') {
            return 'A';
        }

        return  ++$key;
    }
}
