<?php

namespace App\Service;

use App\Entity\WorkoutProgram;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrainerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkoutProgramService
{
    private EntityManagerInterface $entityManager;
    private TrainerRepository $trainerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TrainerRepository $trainerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->trainerRepository = $trainerRepository;
    }

    public function createWorkoutProgram(string $name, string $description, int $duration, int $trainerId): WorkoutProgram
    {
        $trainer = $this->trainerRepository->find($trainerId);

        if (!$trainer) {
            throw new NotFoundHttpException(sprintf('Trainer with ID %d not found.', $trainerId));
        }

        $workoutProgram = new WorkoutProgram();
        $workoutProgram->setName($name)
            ->setDescription($description)
            ->setDuration($duration)
            ->setTrainer($trainer);

        $this->entityManager->persist($workoutProgram);
        $this->entityManager->flush();

        return $workoutProgram;
    }
}
