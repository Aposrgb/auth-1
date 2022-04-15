<?php


namespace App\Service;


use App\Entity\User;
use App\Helper\Exception\ApiException;
use App\Helper\Exception\ResponseCode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate($body = [], $queries = [], $groupsBody = [], $groupsQueries = [])
    {
        $validationError = [];
        $groupsBody[] = 'pagination';
        $groupsQueries[] = 'pagination';
        $bodyError = $this->validator->validate($body, groups: $groupsBody);
        $invalid_field = [];
        /** @var ConstraintViolation $error */
        foreach ($bodyError as $error) {
            $invalid_field[] = [
                'name' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        $validationError['body'] = $invalid_field;


        $queriesError = $this->validator->validate($queries, groups: $groupsQueries);
        $invalid_field = [];
        /** @var ConstraintViolation $error */
        foreach ($queriesError as $error) {
            $invalid_field[] = [
                'name' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        $validationError['query'] = $invalid_field;

        if (count($bodyError) > 0 or count($queriesError) > 0)
            throw new ApiException('Ошибки при выполнении запроса', status: Response::HTTP_BAD_REQUEST);
    }

    public static function validateInteger($object, ExecutionContextInterface $context, $payload)
    {
        if (!is_numeric($object) && $object != "")
            $context->buildViolation('Значение `' . $object . '` не является допустимым int')->addViolation();
    }

    public function checkRequestValidationNotNull($field, $fieldName = null)
    {
        if ($field === null) {
            throw new ApiException(
                message: 'Пустой параметр: ' . $fieldName,
                detail: 'Missing required body',
                status: ResponseCode::HTTP_BAD_REQUEST);
        }
    }

    public function checkTokenUser($user)
    {
        $token = count($user->getApiToken())>0?$user->getApiToken()->last()->getToken(): null;
        if(!$token){
            throw new ApiException(
                message: 'Нету токена для получения статистики',
                detail: 'Missing token',
                status: ResponseCode::HTTP_BAD_REQUEST);
        }
        return $token;
    }
}
