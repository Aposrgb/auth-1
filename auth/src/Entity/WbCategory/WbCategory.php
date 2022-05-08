<?php

namespace App\Entity\WbCategory;

use App\Repository\WbCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WbCategoryRepository::class)]
class WbCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $lastChangeDate;

    #[ORM\OneToMany(mappedBy: 'wbCategory', targetEntity: WbDataCategory::class, cascade: ["persist", "remove"])]
    private $wbCategories;

    public function __construct()
    {
        $this->lastChangeDate = new \DateTime();
        $this->wbCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastChangeDate(): ?\DateTimeInterface
    {
        return $this->lastChangeDate;
    }

    public function setLastChangeDate(\DateTimeInterface $lastChangeDate): self
    {
        $this->lastChangeDate = $lastChangeDate;

        return $this;
    }

    /**
     * @return Collection<int, WbDataCategory>
     */
    public function getWbCategories(): Collection
    {
        return $this->wbCategories;
    }

    public function addWbCategory(WbDataCategory $wbCategory): self
    {
        if (!$this->wbCategories->contains($wbCategory)) {
            $this->wbCategories[] = $wbCategory;
            $wbCategory->setWbCategory($this);
        }

        return $this;
    }

    public function removeWbCategory(WbDataCategory $wbCategory): self
    {
        if ($this->wbCategories->removeElement($wbCategory)) {
            // set the owning side to null (unless already changed)
            if ($wbCategory->getWbCategory() === $this) {
                $wbCategory->setWbCategory(null);
            }
        }

        return $this;
    }
}
