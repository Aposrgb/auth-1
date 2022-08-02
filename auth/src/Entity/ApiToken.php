<?php

namespace App\Entity;

use App\Entity\WbDataEntity\WbData;
use App\Helper\Status\ApiTokenStatus;
use App\Helper\Status\StatusTrait;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    use StatusTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $token;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'apiToken')]
    #[ORM\JoinColumn(nullable: false)]
    private $apiUser;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist'], inversedBy: 'apiToken')]
    private $wbData;

    public function __construct()
    {
        $this->status = ApiTokenStatus::ACTIVE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getApiUser(): ?User
    {
        return $this->apiUser;
    }

    public function setApiUser(?User $apiUser): self
    {
        $this->apiUser = $apiUser;

        return $this;
    }

    public function getWbData(): ?WbData
    {
        return $this->wbData;
    }

    public function setWbData(?WbData $wbData): self
    {
        $this->wbData = $wbData;

        return $this;
    }
}
