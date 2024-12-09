<?php

namespace App\Service;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WorkoutSessionRepository;
use App\Repository\UserRepository;

class BookingService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private WorkoutSessionRepository $workoutSessionRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository,
    WorkoutSessionRepository $workoutSessionRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->workoutSessionRepository = $workoutSessionRepository;
    }

    public function createBooking(int $userId, int $workoutSessionId, string $status): Booking
    {
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
