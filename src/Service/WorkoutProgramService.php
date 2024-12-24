<?php

namespace App\Service;

use App\Entity\WorkoutProgram;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrainerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;

class WorkoutProgramService
{
    private EntityManagerInterface $entityManager;
    private TrainerRepository $trainerRepository;
    private RequestCheckerService $requestChecker;

    public function __construct(
        EntityManagerInterface $entityManager,
        TrainerRepository $trainerRepository, RequestCheckerService $requestChecker

    ) {
        $this->entityManager = $entityManager;
        $this->trainerRepository = $trainerRepository;
        $this->requestChecker = $requestChecker;
    }

    public function createWorkoutProgram(string $name, string $description, int $duration, int $trainerId): WorkoutProgram
    {
        $content = ['name' => $name, 'description' => $description, 'duration' => $duration, 'trainerId' => $trainerId];
        try {
            $this->requestChecker->check($content, ['name', 'description', 'duration', 'trainerId']);
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException('Missing required fields: ' . $e->getMessage());
        }

        try {
            $this->requestChecker->validateRequestDataByConstraints($content, [
                'name' => [new Assert\NotBlank()],
                'description' => [new Assert\NotBlank()],
                'duration' => [new Assert\Positive()],
                'trainerId' => [new Assert\Positive()]
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException $e) {
            throw new NotFoundHttpException('Validation failed: ' . $e->getMessage());
        }


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
