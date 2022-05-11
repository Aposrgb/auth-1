<?php

namespace App\Entity;

use App\Repository\CategorySalesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorySalesRepository::class)]
class CategorySales
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'text')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $thumb;

    #[ORM\Column(type: 'text')]
    private $nmId;

    #[ORM\Column(type: 'text')]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $color;

    #[ORM\Column(type: 'text')]
    private $category;

    #[ORM\Column(type: 'text', nullable: true)]
    private $position;

    #[ORM\Column(type: 'text')]
    private $brand;

    #[ORM\Column(type: 'text')]
    private $seller;

    #[ORM\Column(type: 'text')]
    private $balance;

    #[ORM\Column(type: 'text')]
    private $comments;

    #[ORM\Column(type: 'text', nullable: true)]
    private $rating;

    #[ORM\Column(type: 'text')]
    private $finalPrice;

    #[ORM\Column(type: 'text', nullable: true)]
    private $clientPrice;

    #[ORM\Column(type: 'text')]
    private $dayStock;

    #[ORM\Column(type: 'text')]
    private $sales;

    #[ORM\Column(type: 'text')]
    private $revenue;

    #[ORM\Column(type: 'text')]
    private $graph;

    #[ORM\ManyToOne(targetEntity: DataCategory::class, inversedBy: 'sales')]
    private $wbDataCategory;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $entity;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param mixed $thumb
     * @return CategorySales
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNmId()
    {
        return $this->nmId;
    }

    /**
     * @param mixed $nmId
     * @return CategorySales
     */
    public function setNmId($nmId)
    {
        $this->nmId = $nmId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return CategorySales
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     * @return CategorySales
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return CategorySales
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     * @return CategorySales
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     * @return CategorySales
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @param mixed $seller
     * @return CategorySales
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     * @return CategorySales
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     * @return CategorySales
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     * @return CategorySales
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    /**
     * @param mixed $finalPrice
     * @return CategorySales
     */
    public function setFinalPrice($finalPrice)
    {
        $this->finalPrice = $finalPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientPrice()
    {
        return $this->clientPrice;
    }

    /**
     * @param mixed $clientPrice
     * @return CategorySales
     */
    public function setClientPrice($clientPrice)
    {
        $this->clientPrice = $clientPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDayStock()
    {
        return $this->dayStock;
    }

    /**
     * @param mixed $dayStock
     * @return CategorySales
     */
    public function setDayStock($dayStock)
    {
        $this->dayStock = $dayStock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param mixed $sales
     * @return CategorySales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param mixed $revenue
     * @return CategorySales
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * @param mixed $graph
     * @return CategorySales
     */
    public function setGraph($graph)
    {
        $this->graph = $graph;
        return $this;
    }

    public function getWbDataCategory(): ?DataCategory
    {
        return $this->wbDataCategory;
    }

    public function setWbDataCategory(?DataCategory $wbDataCategory): self
    {
        $this->wbDataCategory = $wbDataCategory;

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
