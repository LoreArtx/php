<?php

namespace App\Entity;

use App\Repository\WorkoutSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkoutSessionRepository::class)]
class WorkoutSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workoutSessions')]
    private ?WorkoutProgram $program = null;

    #[ORM\ManyToOne(inversedBy: 'workoutSessions')]
    private ?Trainer $trainer = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $star_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProgram(): ?WorkoutProgram
    {
        return $this->program;
    }

    public function setProgram(?WorkoutProgram $program): static
    {
        $this->program = $program;

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

    public function getStarTime(): ?\DateTimeInterface
    {
        return $this->star_time;
    }

    public function setStarTime(\DateTimeInterface $star_time): static
    {
        $this->star_time = $star_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }
}
