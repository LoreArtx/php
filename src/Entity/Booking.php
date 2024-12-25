<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['booking:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['booking:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['booking:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['booking:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "User cannot be null.")]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Status cannot be blank.")]
    #[Assert\Choice(choices: ['pending', 'confirmed', 'canceled'], message: "Status must be 'pending', 'confirmed', or 'canceled'.")]
    #[Groups(['booking:read:collection', 'booking:read:item', 'booking:write'])]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: WorkoutSession::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Workout session cannot be null.")]
    private ?WorkoutSession $workoutSession = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getWorkoutSession(): ?WorkoutSession
    {
        return $this->workoutSession;
    }

    public function setWorkoutSession(?WorkoutSession $workoutSession): static
    {
        $this->workoutSession = $workoutSession;
        return $this;
    }
}
