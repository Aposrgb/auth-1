<?php

namespace App\Controller\Api;

use App\Helper\DTO\UserDTO;
use App\Helper\Exception\ApiException;
use App\Security\SecurityService;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Security")
 */
class SecurityApiController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer
    )
    {
    }

    /**
     * @RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(property="username", type="string"),
     *          @OA\Property(property="password", type="string")
     *     )
     * )
     */
    #[Route('/login', name: 'login_api', methods: ['POST'])]
    public function login(Request $request, SecurityService $service): JsonResponse
    {
        if($request->getContent()!=''){
            $user = $this
                ->serializer
                ->deserialize(
                    $request->getContent(), UserDTO::class, "json"
                );
        }else{
            throw new ApiException("Not found name or password");
        }

        return $this->json(['data' => ['token' => $service->login($user)]]);
    }
}