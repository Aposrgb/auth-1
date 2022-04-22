<?php

namespace App\Entity\WbDataEntity;

use App\Repository\WbDataPropertyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WbDataPropertyRepository::class)]
class WbDataProperty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $property;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist', 'remove'], inversedBy: 'wbDataSales')]
    private $wbDataSale;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist', 'remove'], inversedBy: 'wbDataOrders')]
    private $wbDataOrder;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist', 'remove'], inversedBy: 'wbDataIncomes')]
    private $wbDataIncome;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist', 'remove'], inversedBy: 'wbDataStock')]
    private $wbDataStock;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist', 'remove'], inversedBy: 'wbDataReports')]
    private $wbDataReport;

    #[ORM\ManyToOne(targetEntity: WbData::class, cascade: ['persist', 'remove'], inversedBy: 'wbDataExcises')]
    private $wbDataExcise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(string $sale): self
    {
        $this->property = $sale;

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

    /**
     * @return mixed
     */
    public function getWbDataSale()
    {
        return $this->wbDataSale;
    }

    /**
     * @param mixed $wbDataSale
     * @return WbDataProperty
     */
    public function setWbDataSale($wbDataSale)
    {
        $this->wbDataSale = $wbDataSale;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWbDataOrder()
    {
        return $this->wbDataOrder;
    }

    /**
     * @param mixed $wbDataOrder
     * @return WbDataProperty
     */
    public function setWbDataOrder($wbDataOrder)
    {
        $this->wbDataOrder = $wbDataOrder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWbDataIncome()
    {
        return $this->wbDataIncome;
    }

    /**
     * @param mixed $wbDataIncome
     * @return WbDataProperty
     */
    public function setWbDataIncome($wbDataIncome)
    {
        $this->wbDataIncome = $wbDataIncome;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWbDataStock()
    {
        return $this->wbDataStock;
    }

    /**
     * @param mixed $wbDataStock
     * @return WbDataProperty
     */
    public function setWbDataStock($wbDataStock)
    {
        $this->wbDataStock = $wbDataStock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWbDataReport()
    {
        return $this->wbDataReport;
    }

    /**
     * @param mixed $wbDataReport
     * @return WbDataProperty
     */
    public function setWbDataReport($wbDataReport)
    {
        $this->wbDataReport = $wbDataReport;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWbDataExcise()
    {
        return $this->wbDataExcise;
    }

    /**
     * @param mixed $wbDataExcise
     * @return WbDataProperty
     */
    public function setWbDataExcise($wbDataExcise)
    {
        $this->wbDataExcise = $wbDataExcise;
        return $this;
    }


}
