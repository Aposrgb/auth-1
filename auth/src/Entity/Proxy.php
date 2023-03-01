<?php

namespace App\Entity;

use App\Repository\ProxyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProxyRepository::class)]
class Proxy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $loginPass;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $ipPort;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLoginPass(): ?string
    {
        return $this->loginPass;
    }

    /**
     * @param string|null $loginPass
     * @return Proxy
     */
    public function setLoginPass(?string $loginPass): Proxy
    {
        $this->loginPass = $loginPass;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIpPort(): ?string
    {
        return $this->ipPort;
    }

    /**
     * @param string|null $ipPort
     * @return Proxy
     */
    public function setIpPort(?string $ipPort): Proxy
    {
        $this->ipPort = $ipPort;
        return $this;
    }

    public function getLogPassWithIpPort(): string
    {
        $res = '';
        if ($this->loginPass) {
            $res = $this->loginPass . '@';
        }
        if ($this->ipPort) {
            $res .= $this->ipPort;
        }
        return $res;
    }
}