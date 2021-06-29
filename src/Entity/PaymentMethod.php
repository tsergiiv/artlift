<?php

namespace App\Entity;

use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentMethodRepository::class)
 */
class PaymentMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $cardNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $expMonth;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $expYear;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $cvv;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getExpMonth(): ?string
    {
        return $this->expMonth;
    }

    public function setExpMonth(string $expMonth): self
    {
        $this->expMonth = $expMonth;

        return $this;
    }

    public function getExpYear(): ?string
    {
        return $this->expYear;
    }

    public function setExpYear(string $expYear): self
    {
        $this->expYear = $expYear;

        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;

        return $this;
    }
}
