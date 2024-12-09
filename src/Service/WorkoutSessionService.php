<?php

namespace App\Service;

use App\Entity\WorkoutSession;
use App\Entity\WorkoutProgram;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WorkoutProgramRepository;
use App\Repository\TrainerRepository;

class WorkoutSessionService
{
    private EntityManagerInterface $entityManager;
    private WorkoutProgramRepository $workoutProgramRepository;
    private TrainerRepository $trainerRepository;

    public function __construct(EntityManagerInterface $entityManager, WorkoutProgramRepository $workoutProgramRepository, TrainerRepository $trainerRepository)
    {
        $this->entityManager = $entityManager;
        $this->workoutProgramRepository = $workoutProgramRepository;
        $this->trainerRepository = $trainerRepository;
    }

    public function createWorkoutSession(
        int $programId,
        int $trainerId,
        \DateTimeInterface $startTime,
        \DateTimeInterface $endTime
    ): WorkoutSession {
        $workoutSession = new WorkoutSession();

        $program = $this->workoutProgramRepository->find($programId);
        $trainer = $this->trainerRepository->find($trainerId);

        if (!$program || !$trainer) {
            throw new \InvalidArgumentException('Invalid Program or Trainer ID provided.');
        }

        $workoutSession->setProgram($program)
            ->setTrainer($trainer)
            ->setStartTime($startTime)
            ->setEndTime($endTime);

        $this->entityManager->persist($workoutSession);
        $this->entityManager->flush();

        return $workoutSession;
    }
}
