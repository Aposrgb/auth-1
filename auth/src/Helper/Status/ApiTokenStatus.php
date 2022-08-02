<?php

namespace App\Helper\Status;

class ApiTokenStatus extends AbstractStatus
{
    public const ACTIVE = 1;
    public const UPDATING = 2;
    public const BLOCK = 11;

    protected static $statusNames = [
        self::ACTIVE => 'Активен',
        self::BLOCK => 'Заблокирован',
        self::UPDATING => 'Обновление данных'
    ];

    protected static $statusTypes = [
        self::ACTIVE => 'primary',
        self::BLOCK => 'warning',
        self::UPDATING => 'primary',
    ];
}