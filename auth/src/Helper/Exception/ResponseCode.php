<?php


namespace App\Helper\Exception;


use Symfony\Component\HttpFoundation\Response;

class ResponseCode extends Response
{
    public const HTTP_VALIDATION_ERROR = 461;

    public static $statusTexts = [
        self::HTTP_VALIDATION_ERROR => 'Validation Data Error'
    ];

    public static function getStatusTexts(): array
    {
        return parent::$statusTexts + self::$statusTexts;
    }
}