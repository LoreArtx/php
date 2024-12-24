<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[Assert\NotNull(message: "User must be specified.")]
    private ?User $user = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: "Amount is required.")]
    #[Assert\Positive(message: "Amount must be a positive value.")]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "Payment date is required.")]
    #[Assert\LessThanOrEqual('now', message: "Payment date cannot be in the future.")]
    private ?\DateTimeInterface $payment_date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Status is required.")]
    #[Assert\Choice(choices: ['pending', 'completed', 'failed'], message: "Status must be one of 'pending', 'completed', or 'failed'.")]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[Assert\NotNull(message: "Membership must be specified.")]
    private ?Membership $membership = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeInterface $payment_date): static
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(?Membership $membership): static
    {
        $this->membership = $membership;

        return $this;
    }
}
