<?php

namespace App\Entity;

use App\Helper\Status\DeviceStatus;
use App\Helper\Status\StatusTrait;
use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    use StatusTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist", "remove"], inversedBy: 'devices')]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $token;

    #[ORM\Column(type: 'datetime')]
    private $dateExpires;

    public function __construct()
    {
        $this->token = (md5(microtime() . rand(100000, 10000000)));
        $this->dateExpires = (new \DateTime())->modify("+ 7 day");
        $this->status = DeviceStatus::ACTIVE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getDateExpires(): ?\DateTimeInterface
    {
        return $this->dateExpires;
    }

    public function setDateExpires(\DateTimeInterface $dateExpires): self
    {
        $this->dateExpires = $dateExpires;

        return $this;
    }
}