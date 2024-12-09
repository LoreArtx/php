<?php

namespace App\Service;

use App\Entity\Feedback;
use App\Entity\User;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\TrainerRepository;
    
class FeedbackService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private TrainerRepository $trainerRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, TrainerRepository $trainerRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->trainerRepository = $trainerRepository;
    }

    public function createFeedback(int $userID, int $trainerID, int $rating, string $comment): Feedback
    {
        $feedback = new Feedback();
        $user = $this->userRepository->find($userID);
        $trainer = $this->trainerRepository->find($trainerID);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User with ID %d not found.', $userID));
        }

        if (!$trainer) {
            throw new NotFoundHttpException(sprintf('Trainer with ID %d not found.', $trainerID));
        }

        if ($user && $trainer) {
            $feedback->setUser($user)
            ->setTainer($trainer)
            ->setRating($rating)
            ->setComment($comment);

            $this->entityManager->persist($feedback);
            $this->entityManager->flush();

            return $feedback;  
        }

    }
}
