<?php

namespace App\Enums;

use ReflectionClass;

class RegistrationStatus
{
    public const REGISTERED = 'registered';
    public const CHECKED_IN = 'checked_in';
    public const SHOT = 'shot';
    public const CANCELED = 'canceled';

    private static function getConstants()
    {
        $oClass = new ReflectionClass(self::class);

        return $oClass->getConstants();
    }

    public static function allCases()
    {
        $consts = self::getConstants();
        $array = [];
        foreach ($consts as $properties => $value) {
            array_push($array, $value);
        }

        return $array;
    }
}
