<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Name cannot be blank.")]
    #[Assert\Length(
        min: 3,
        minMessage: "Name must be at least 3 characters long.",
        max: 255,
        maxMessage: "Name cannot exceed 255 characters."
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Type cannot be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Type cannot exceed 255 characters."
    )]
    private ?string $type = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Quantity cannot be blank.")]
    #[Assert\Positive(message: "Quantity must be positive.")]
    private ?int $quantity = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Status cannot be blank.")]
    #[Assert\Choice(
        choices: ['available', 'unavailable'],
        message: "Status must be 'available' or 'unavailable'."
    )]
    private ?string $status = null;

    /**
     * @var Collection<int, WorkoutSession>
     */
    #[ORM\ManyToMany(targetEntity: WorkoutSession::class, mappedBy: 'equipment')]
    private Collection $workoutSessions;

    public function __construct()
    {
        $this->workoutSessions = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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

    /**
     * @return Collection<int, WorkoutSession>
     */
    public function getWorkoutSessions(): Collection
    {
        return $this->workoutSessions;
    }

    public function addWorkoutSession(WorkoutSession $workoutSession): static
    {
        if (!$this->workoutSessions->contains($workoutSession)) {
            $this->workoutSessions->add($workoutSession);
            $workoutSession->addEquipment($this);
        }

        return $this;
    }

    public function removeWorkoutSession(WorkoutSession $workoutSession): static
    {
        if ($this->workoutSessions->removeElement($workoutSession)) {
            $workoutSession->removeEquipment($this);
        }

        return $this;
    }
}
