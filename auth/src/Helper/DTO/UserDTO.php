<?php

namespace App\Helper\DTO;

class UserDTO
{
    private $username;

    private $password;

    private $dateExpired;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDateExpired(): ?\DateTimeInterface
    {
        return $this->dateExpired;
    }

    public function setDateExpired(\DateTimeInterface $dateExpired): self
    {
        $this->dateExpired = $dateExpired;

        return $this;
    }
}