<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SubscriptionPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['subscription_plan:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['subscription_plan:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['subscription_plan:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['subscription_plan:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: SubscriptionPlanRepository::class)]
class SubscriptionPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The name of the subscription plan is required.")]
    #[Assert\Length(max: 255, maxMessage: "The name cannot exceed 255 characters.")]
    #[Groups(['subscription_plan:read:collection', 'subscription_plan:read:item', 'subscription_plan:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: "The price is required.")]
    #[Assert\PositiveOrZero(message: "The price must be a positive value or zero.")]
    #[Groups(['subscription_plan:read:item', 'subscription_plan:write'])]
    private ?string $price = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message: "The duration is required.")]
    #[Assert\Positive(message: "The duration must be a positive integer.")]
    #[Groups(['subscription_plan:read:item', 'subscription_plan:write'])]
    private ?int $duration = null;

    /**
     * @var Collection<int, Membership>
     */
    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: 'subscriptionPlan')]
    #[Groups(['subscription_plan:read:item'])]
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
