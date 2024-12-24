<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "User cannot be null.")]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Status cannot be blank.")]
    #[Assert\Choice(
        choices: ['pending', 'confirmed', 'canceled'],
        message: "Status must be 'pending', 'confirmed', or 'canceled'."
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "Status cannot exceed 255 characters."
    )]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: WorkoutSession::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Workout session cannot be null.")]
    private ?WorkoutSession $workoutSession = null;

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
