<?php

namespace App\Helper\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Annotation\Groups;

class ApiException extends HttpException
{
    /**
     * @var string|null
     * @Groups({"show"})
     */
    protected $message;

    /**
     * @var string|null
     * @Groups({"show"})
     */
    protected ?string $detail;

    /**
     * @var array|null
     * @Groups({"show"})
     */
    protected ?array $validationErrors = [];

    /**
     * @var int
     * @Groups({"show"})
     */
    protected $status;

    public function __construct(?string $message = null,
                                ?string $detail = null,
                                int $status = Response::HTTP_BAD_REQUEST,
                                ?array $validationErrors = [],
                                HttpException $previous = null,
                                array $headers = [],
                                ?int $code = 0
    )
    {
        $this->message = is_null($message) ?
            ResponseCode::getStatusTexts()[$status] ?? ResponseCode::getStatusTexts()[ResponseCode::HTTP_INTERNAL_SERVER_ERROR] :
            $message;
        $this->detail = $detail;
        $this->status = $status;
        $this->validationErrors = $validationErrors;
        parent::__construct($status, $message, $previous, $headers, $code);
    }

    public function responseBody() {
        return [
            'error' => [
                'status' => $this->status,
                'message' => $this->message,
                'detail' => $this->detail,
                'validationErrors' => $this->validationErrors
            ]
        ];
    }
}