<?php

namespace App\Service;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WorkoutSessionRepository;
use App\Repository\UserRepository;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private WorkoutSessionRepository $workoutSessionRepository;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository,
    WorkoutSessionRepository $workoutSessionRepository, RequestCheckerService $requestChecker)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->workoutSessionRepository = $workoutSessionRepository;
        $this->requestChecker = $requestChecker;
    }

    public function createBooking(int $userId, int $workoutSessionId, string $status): Booking
    {
        $this->requestChecker->check([
            'userId' => $userId,
            'workoutSessionId' => $workoutSessionId,
            'status' => $status
        ], ['userId', 'workoutSessionId', 'status']);

        $this->requestChecker->validateRequestDataByConstraints($status, [
            'status' => [
                new Assert\NotBlank(),
                new Assert\Choice(['values' => ['pending', 'confirmed', 'canceled']])
            ]
        ]);


        $booking = new Booking();
        $user = $this->userRepository->find($userId);
        $workoutSession = $this->workoutSessionRepository->find($workoutSessionId);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User with ID %d not found.', $userID));
        }

        if (!$workoutSession) {
            throw new NotFoundHttpException(sprintf('WorkoutSession with ID %d not found.', $workoutSessionId));
        }

        $booking->setUser($user)
                ->setWorkoutSession($workoutSession)
                ->setStatus($status);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }
}
