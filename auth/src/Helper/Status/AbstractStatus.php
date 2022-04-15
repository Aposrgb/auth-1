<?php

namespace App\Helper\Status;


abstract class AbstractStatus
{
    public const ACTIVE = 1;
    public const BLOCK = 11;
    public const ARCHIVE = 21;

    protected static $statusNames = [
        self::ACTIVE => 'Активен',
        self::ARCHIVE => 'В архиве',
        self::BLOCK => 'Заблокирован'
    ];

    protected static $statusTypes = [
        self::ACTIVE => 'primary',
        self::BLOCK => 'warning',
        self::ARCHIVE => 'danger',
    ];

    public static function getName($key)
    {
        if (isset(static::$statusNames)) {
            if (array_key_exists($key, static::$statusNames)) {
                return static::$statusNames[$key];
            }
        }

        return '';
    }

    public static function getType($key)
    {
        if (isset(static::$statusTypes)) {
            if (array_key_exists($key, static::$statusTypes)) {
                return static::$statusTypes[$key];
            }
        }

        return '';
    }

    public static function getStatusNames()
    {
        return static::$statusNames;
    }
}