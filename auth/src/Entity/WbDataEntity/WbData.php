<?php

namespace App\Entity\WbDataEntity;

use App\Entity\ApiToken;
use App\Repository\WbDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WbDataRepository::class)]
class WbData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(mappedBy: 'wbData', targetEntity: ApiToken::class, cascade: ['persist'])]
    private $apiToken;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\OneToMany(mappedBy: 'wbDataSale', targetEntity: WbDataProperty::class, cascade: ['persist', 'remove'])]
    private $wbDataSales;

    #[ORM\OneToMany(mappedBy: 'wbDataOrder', targetEntity: WbDataProperty::class, cascade: ['persist', 'remove'])]
    private $wbDataOrders;

    #[ORM\OneToMany(mappedBy: 'wbDataIncome', targetEntity: WbDataProperty::class, cascade: ['persist', 'remove'])]
    private $wbDataIncomes;

    #[ORM\OneToMany(mappedBy: 'wbDataStock', targetEntity: WbDataProperty::class, cascade: ['persist', 'remove'])]
    private $wbDataStocks;

    #[ORM\OneToMany(mappedBy: 'wbDataReport', targetEntity: WbDataProperty::class, cascade: ['persist', 'remove'])]
    private $wbDataReports;

    #[ORM\OneToMany(mappedBy: 'wbDataExcise', targetEntity: WbDataProperty::class, cascade: ['persist', 'remove'])]
    private $wbDataExcises;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->apiToken = new ArrayCollection();
        $this->wbDataSales = new ArrayCollection();
        $this->wbDataOrders = new ArrayCollection();
        $this->wbDataExcises = new ArrayCollection();
        $this->wbDataStocks = new ArrayCollection();
        $this->wbDataIncomes = new ArrayCollection();
        $this->wbDataReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiToken(): Collection
    {
        return $this->wbDataExcises;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiToken->contains($apiToken)) {
            $this->apiToken[] = $apiToken;
            $apiToken->setWbData($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiToken->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getWbData() === $this) {
                $apiToken->setWbData(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getWbDataSales(): Collection
    {
        return $this->wbDataSales;
    }

    public function addWbDataSale(WbDataProperty $wbDataSale): self
    {
        if (!$this->wbDataSales->contains($wbDataSale)) {
            $this->wbDataSales[] = $wbDataSale;
            $wbDataSale->setWbData($this);
        }

        return $this;
    }

    public function removeWbDataSale(WbDataProperty $wbDataSale): self
    {
        if ($this->wbDataSales->removeElement($wbDataSale)) {
            // set the owning side to null (unless already changed)
            if ($wbDataSale->getWbData() === $this) {
                $wbDataSale->setWbData(null);
            }
        }

        return $this;
    }

    public function getWbDataOrders(): Collection
    {
        return $this->wbDataOrders;
    }

    public function addWbDataOrder(WbDataProperty $wbDataOrder): self
    {
        if (!$this->wbDataOrders->contains($wbDataOrder)) {
            $this->wbDataOrders[] = $wbDataOrder;
            $wbDataOrder->setWbData($this);
        }

        return $this;
    }

    public function removeWbDataOrder(WbDataProperty $wbDataOrder): self
    {
        if ($this->wbDataOrders->removeElement($wbDataOrder)) {
            // set the owning side to null (unless already changed)
            if ($wbDataOrder->getWbData() === $this) {
                $wbDataOrder->setWbData(null);
            }
        }

        return $this;
    }

    public function getWbDataIncomes(): Collection
    {
        return $this->wbDataIncomes;
    }

    public function addWbDataIncome(WbDataProperty $wbDataIncome): self
    {
        if (!$this->wbDataIncomes->contains($wbDataIncome)) {
            $this->wbDataIncomes[] = $wbDataIncome;
            $wbDataIncome->setWbData($this);
        }

        return $this;
    }

    public function removeWbDataIncome(WbDataProperty $wbDataIncome): self
    {
        if ($this->wbDataIncomes->removeElement($wbDataIncome)) {
            // set the owning side to null (unless already changed)
            if ($wbDataIncome->getWbData() === $this) {
                $wbDataIncome->setWbData(null);
            }
        }

        return $this;
    }

    public function getWbDataStocks(): Collection
    {
        return $this->wbDataStocks;
    }

    public function addWbDataStock(WbDataProperty $wbDataStock): self
    {
        if (!$this->wbDataStocks->contains($wbDataStock)) {
            $this->wbDataStocks[] = $wbDataStock;
            $wbDataStock->setWbData($this);
        }

        return $this;
    }

    public function removeWbDataStock(WbDataProperty $wbDataStock): self
    {
        if ($this->wbDataStocks->removeElement($wbDataStock)) {
            // set the owning side to null (unless already changed)
            if ($wbDataStock->getWbData() === $this) {
                $wbDataStock->setWbData(null);
            }
        }

        return $this;
    }

    public function getWbDataReports(): Collection
    {
        return $this->wbDataReports;
    }

    public function addWbDataReport(WbDataProperty $wbDataReport): self
    {
        if (!$this->wbDataReports->contains($wbDataReport)) {
            $this->wbDataReports[] = $wbDataReport;
            $wbDataReport->setWbData($this);
        }

        return $this;
    }

    public function removeWbDataReport(WbDataProperty $wbDataReport): self
    {
        if ($this->wbDataReports->removeElement($wbDataReport)) {
            // set the owning side to null (unless already changed)
            if ($wbDataReport->getWbData() === $this) {
                $wbDataReport->setWbData(null);
            }
        }

        return $this;
    }

    public function getWbDataExcises(): Collection
    {
        return $this->wbDataExcises;
    }

    public function addWbDataExcise(WbDataProperty $wbDataExcise): self
    {
        if (!$this->wbDataExcises->contains($wbDataExcise)) {
            $this->wbDataExcises[] = $wbDataExcise;
            $wbDataExcise->setWbData($this);
        }

        return $this;
    }

    public function removeWbDataExcise(WbDataProperty $wbDataExcise): self
    {
        if ($this->wbDataExcises->removeElement($wbDataExcise)) {
            // set the owning side to null (unless already changed)
            if ($wbDataExcise->getWbData() === $this) {
                $wbDataExcise->setWbData(null);
            }
        }

        return $this;
    }

}

