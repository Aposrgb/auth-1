<?php

namespace App\Helper\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ApiExceptionHandler
 * @package App\Helper\Exception
 * @deprecated
 */
class ApiExceptionHandler
{
    public static function errorApiHandlerValidatorMessage(
        ConstraintViolationListInterface $errors,
                                         $code = Response::HTTP_INTERNAL_SERVER_ERROR,
                                         $customMessage=null
    )
    {

        $invalid_field = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $invalid_field[] = $error->getPropertyPath() . '(' . ($error->getMessage()) . ')';
        }
        $detail = 'Invalid fields: ' . implode('; ', $invalid_field);
        self::errorApiHandlerMessage($customMessage, $detail, $code);
    }

    public static function errorApiHandlerObjectNotFoundMessage(
        $field,
        $code = Response::HTTP_NOT_FOUND,
        $customMessage=null
    )
    {
        $detail = 'Object Not Found by fields: ' . $field;
        self::errorApiHandlerMessage($customMessage, $detail, $code);
    }

    public static function errorApiHandlerRequiredBodyMessage($code = Response::HTTP_BAD_REQUEST, $customMessage=null) {
        $detail = 'Missing required body';
        self::errorApiHandlerMessage($customMessage, $detail, $code);
    }

    public static function errorApiHandlerRequiredFieldsMessage(
        $fields,
        $code = Response::HTTP_BAD_REQUEST,
        $customMessage=null
    )
    {
        $detail = 'Missing required fields: ' . implode(', ', $fields);
        self::errorApiHandlerMessage($customMessage, $detail, $code);
    }

    public static function errorApiHandlerMessage($customMessage=null, $detail = '', $code = Response::HTTP_BAD_REQUEST)
    {
        $message = is_null($customMessage) ? ResponseCode::getStatusTexts()[$code] : $customMessage;
        throw new ApiException($message, $detail, $code);
    }
}