<?php

namespace App\Entity;

use App\Repository\DataCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataCategoryRepository::class)]
class DataCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $path;

    #[ORM\Column(type: 'string', length: 255)]
    private $url;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ["persist"],  inversedBy: 'wbCategories')]
    private $wbCategory;

    #[ORM\OneToMany(mappedBy: 'wbDataCategory', targetEntity: CategorySales::class, cascade: ["persist", "remove"])]
    private $sales;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $entity;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getWbCategory(): ?Category
    {
        return $this->wbCategory;
    }

    public function setWbCategory(?Category $wbCategory): self
    {
        $this->wbCategory = $wbCategory;

        return $this;
    }

    /**
     * @return Collection<int, CategorySales>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(CategorySales $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setWbDataCategory($this);
        }

        return $this;
    }

    public function removeSale(CategorySales $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getWbDataCategory() === $this) {
                $sale->setWbDataCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }


}
