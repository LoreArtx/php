<?php

namespace App\Entity;

use App\Repository\SubscriptionPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionPlanRepository::class)]
class SubscriptionPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $duration = null;

    /**
     * @var Collection<int, Membership>
     */
    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: 'subscriptionPlan')]
    private Collection $membership;

    public function __construct()
    {
        $this->membership = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Membership>
     */
    public function getMembership(): Collection
    {
        return $this->membership;
    }

    public function addMembership(Membership $membership): static
    {
        if (!$this->membership->contains($membership)) {
            $this->membership->add($membership);
            $membership->setSubscriptionPlan($this);
        }

        return $this;
    }

    public function removeMembership(Membership $membership): static
    {
        if ($this->membership->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getSubscriptionPlan() === $this) {
                $membership->setSubscriptionPlan(null);
            }
        }

        return $this;
    }
}
