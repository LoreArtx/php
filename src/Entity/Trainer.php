<?php

namespace App\Entity;

use App\Repository\TrainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrainerRepository::class)]
class Trainer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Name is required.")]
    #[Assert\Length(max: 255, maxMessage: "Name cannot exceed 255 characters.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Specialization is required.")]
    private ?string $specialization = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Experience is required.")]
    #[Assert\PositiveOrZero(message: "Experience must be a positive number or zero.")]
    private ?int $experience = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Phone is required.")]
    #[Assert\Regex(
        pattern: "/^\+?\d{10,15}$/",
        message: "Phone number must be valid and contain 10-15 digits."
    )]
    private ?string $phone = null;

    /**
     * @var Collection<int, WorkoutProgram>
     */
    #[ORM\OneToMany(targetEntity: WorkoutProgram::class, mappedBy: 'trainer')]
    private Collection $workoutPrograms;

    /**
     * @var Collection<int, WorkoutSession>
     */
    #[ORM\OneToMany(targetEntity: WorkoutSession::class, mappedBy: 'trainer')]
    private Collection $workoutSessions;

    /**
     * @var Collection<int, Feedback>
     */
    #[ORM\OneToMany(targetEntity: Feedback::class, mappedBy: 'trainer')]
    private Collection $feedback;

    public function __construct()
    {
        $this->workoutPrograms = new ArrayCollection();
        $this->workoutSessions = new ArrayCollection();
        $this->feedback = new ArrayCollection();
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

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): static
    {
        $this->specialization = $specialization;

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): static
    {
        $this->experience = $experience;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, WorkoutProgram>
     */
    public function getWorkoutPrograms(): Collection
    {
        return $this->workoutPrograms;
    }

    public function addWorkoutProgram(WorkoutProgram $workoutProgram): static
    {
        if (!$this->workoutPrograms->contains($workoutProgram)) {
            $this->workoutPrograms->add($workoutProgram);
            $workoutProgram->setTrainer($this);
        }

        return $this;
    }

    public function removeWorkoutProgram(WorkoutProgram $workoutProgram): static
    {
        if ($this->workoutPrograms->removeElement($workoutProgram)) {
            if ($workoutProgram->getTrainer() === $this) {
                $workoutProgram->setTrainer(null);
            }
        }

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
            $workoutSession->setTrainer($this);
        }

        return $this;
    }

    public function removeWorkoutSession(WorkoutSession $workoutSession): static
    {
        if ($this->workoutSessions->removeElement($workoutSession)) {
            if ($workoutSession->getTrainer() === $this) {
                $workoutSession->setTrainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setTrainer($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            if ($feedback->getTrainer() === $this) {
                $feedback->setTrainer(null);
            }
        }

        return $this;
    }
}
