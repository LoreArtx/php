<?php

namespace App\Entity;

use App\Repository\WorkoutProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkoutProgramRepository::class)]
class WorkoutProgram
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Program name is required.")]
    #[Assert\Length(max: 255, maxMessage: "Program name cannot exceed 255 characters.")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Description is required.")]
    #[Assert\Length(max: 5000, maxMessage: "Description cannot exceed 5000 characters.")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Duration is required.")]
    #[Assert\Positive(message: "Duration must be a positive integer.")]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'workoutPrograms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainer $trainer = null;

    /**
     * @var Collection<int, WorkoutSession>
     */
    #[ORM\OneToMany(targetEntity: WorkoutSession::class, mappedBy: 'program', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $workoutSessions;

    public function __construct()
    {
        $this->workoutSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    public function setTrainer(?Trainer $trainer): static
    {
        $this->trainer = $trainer;

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
            $workoutSession->setProgram($this);
        }

        return $this;
    }

    public function removeWorkoutSession(WorkoutSession $workoutSession): static
    {
        if ($this->workoutSessions->removeElement($workoutSession)) {
            if ($workoutSession->getProgram() === $this) {
                $workoutSession->setProgram(null);
            }
        }

        return $this;
    }
}
