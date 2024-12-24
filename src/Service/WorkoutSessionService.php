<?php

namespace App\Service;

use App\Entity\WorkoutSession;
use App\Entity\WorkoutProgram;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WorkoutProgramRepository;
use App\Repository\TrainerRepository;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkoutSessionService
{
    private EntityManagerInterface $entityManager;
    private WorkoutProgramRepository $workoutProgramRepository;
    private TrainerRepository $trainerRepository;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, WorkoutProgramRepository $workoutProgramRepository, TrainerRepository $trainerRepository, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->workoutProgramRepository = $workoutProgramRepository;
        $this->trainerRepository = $trainerRepository;
        $this->requestChecker = $requestChecker;
    }

    public function createWorkoutSession(
        int $programId,
        int $trainerId,
        \DateTimeInterface $startTime,
        \DateTimeInterface $endTime
    ): WorkoutSession {

        $content = [
            'programId' => $programId,
            'trainerId' => $trainerId,
            'startTime' => $startTime,
            'endTime' => $endTime
        ];

        try {
            $this->requestChecker->check($content, ['programId', 'trainerId', 'startTime', 'endTime']);
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException('Missing required fields: ' . $e->getMessage());
        }
        try {
            $this->requestChecker->validateRequestDataByConstraints($content, [
                'programId' => [new Assert\Positive()],
                'trainerId' => [new Assert\Positive()],
                'startTime' => [new Assert\Type('DateTime')],
                'endTime' => [new Assert\Type('DateTime')]
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException $e) {
            throw new NotFoundHttpException('Validation failed: ' . $e->getMessage());
        }


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
