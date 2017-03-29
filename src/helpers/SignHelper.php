<?php

namespace michaeldomo\robokassa\helpers;

/**
 * Class SignHelper
 * @package michaeldomo\robokassa\helpers
 */
class SignHelper
{
    /**
     * @param $shp array
     * @return string
     */
    public static function implodeShp($shp)
    {
        ksort($shp);
        foreach ($shp as $key => $value) {
            $shp[$key] = $key . '=' . $value;
        }

        return implode(':', $shp);
    }
}
