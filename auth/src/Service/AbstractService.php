<?php

namespace App\Service;

use App\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer,
        protected UserPasswordHasherInterface $hasher,
        protected $mpStatsApi,
        protected $mpStatsApiWb
    )
    {
    }

    protected function checkStatusToken($id, $query = [])
    {
        $tokens = $this
            ->entityManager
            ->getRepository(ApiToken::class)
            ->getTokenWithUser($id, true);

        $token = !key_exists('index', $query) ? ($tokens[0] ?? null) : $tokens[$query['index']];
        return $token ?
            ['wbData' => $token->getWbData(), 'token' => $token, 'tokens' => $tokens] :
            ['wbData' => null, 'token' => null];
    }
}