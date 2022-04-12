<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToMany(mappedBy: 'apiUser', targetEntity: ApiToken::class, cascade: ['persist', 'remove'])]
    private $apiToken;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateExpired;

    public function __construct()
    {
        $this->apiToken = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    public function addRole($role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiToken(): Collection
    {
        return $this->apiToken;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiToken->contains($apiToken)) {
            $this->apiToken[] = $apiToken;
            $apiToken->setApiUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiToken->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getApiUser() === $this) {
                $apiToken->setApiUser(null);
            }
        }

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
