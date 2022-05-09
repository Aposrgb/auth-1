<?php

namespace App\Entity\WbCategory;

use App\Repository\WbCategorySalesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WbCategorySalesRepository::class)]
class WbCategorySales
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $thumb;

    #[ORM\Column(type: 'integer')]
    private $nmId;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $color;

    #[ORM\Column(type: 'string', length: 255)]
    private $category;

    #[ORM\Column(type: 'integer')]
    private $position;

    #[ORM\Column(type: 'string', length: 255)]
    private $brand;

    #[ORM\Column(type: 'string', length: 255)]
    private $seller;

    #[ORM\Column(type: 'integer')]
    private $balance;

    #[ORM\Column(type: 'integer')]
    private $comments;

    #[ORM\Column(type: 'integer')]
    private $rating;

    #[ORM\Column(type: 'integer')]
    private $finalPrice;

    #[ORM\Column(type: 'integer')]
    private $clientPrice;

    #[ORM\Column(type: 'integer')]
    private $dayStock;

    #[ORM\Column(type: 'integer')]
    private $sales;

    #[ORM\Column(type: 'integer')]
    private $revenue;

    #[ORM\Column(type: 'string', length: 255)]
    private $graph;

    #[ORM\ManyToOne(targetEntity: WbDataCategory::class, inversedBy: 'sales')]
    private $wbDataCategory;

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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
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
     * @return WbCategorySales
     */
    public function setGraph($graph)
    {
        $this->graph = $graph;
        return $this;
    }

    public function getWbDataCategory(): ?WbDataCategory
    {
        return $this->wbDataCategory;
    }

    public function setWbDataCategory(?WbDataCategory $wbDataCategory): self
    {
        $this->wbDataCategory = $wbDataCategory;

        return $this;
    }
}
