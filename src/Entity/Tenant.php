<?php

namespace App\Entity;

use App\Repository\TenantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TenantRepository::class)
 */
class Tenant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="float")
     */
    private $rental;

    /**
     * @ORM\Column(type="float")
     */
    private $charge;

    /**
     * @ORM\ManyToOne(targetEntity=Local::class, inversedBy="tenants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $local;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sendReceipt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getRental(): ?float
    {
        return $this->rental;
    }

    public function setRental(float $rental): self
    {
        $this->rental = $rental;

        return $this;
    }

    public function getCharge(): ?float
    {
        return $this->charge;
    }

    public function setCharge(float $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getLocal(): ?local
    {
        return $this->local;
    }

    public function setLocal(?local $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getSendReceipt(): ?bool
    {
        return $this->sendReceipt;
    }

    public function setSendReceipt(bool $sendReceipt): self
    {
        $this->sendReceipt = $sendReceipt;

        return $this;
    }
}
