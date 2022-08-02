<?php

namespace App\Helper\Status;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait StatusTrait
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'integer')]
    #[Groups(["show"])]
    protected $status;

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int)$this->status;
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getStatusClass()
    {
        $function = new \ReflectionClass($this);
        $statusClassName = 'App\Helper\Status\\' . $function->getShortName() . "Status";
        $statusClass = new $statusClassName;
        return $statusClass;
    }

    /**
     * @return string
     * @throws \ReflectionException
     * @Groups({"show"})
     */
    public function getStatusName()
    {
        $statusClass = $this->getStatusClass();
        return $statusClass::getName($this->getStatus());
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function getStatusType()
    {
        return $this->getStatusClass()->getType($this->getStatus());
    }

    /**
     * @return bool
     */
    public function editAllowed()
    {
        return true;
    }

    public function isActive()
    {
        return $this->getStatus() == AbstractStatus::ACTIVE;
    }
}