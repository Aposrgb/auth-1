<?php

namespace App\Helper\Status;

class UserStatus extends AbstractStatus
{
    public const ACTIVE = 1;
    public const DEMO = 2;
    public const BLOCK = 11;
    public const ARCHIVE = 21;

    protected static $statusNames = [
        self::ACTIVE => 'Активен',
        self::DEMO => 'Демо',
        self::ARCHIVE => 'В архиве',
        self::BLOCK => 'Заблокирован'
    ];
}