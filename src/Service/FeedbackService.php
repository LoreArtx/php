<?php

namespace App\Service;

use App\Entity\Feedback;
use App\Entity\User;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\TrainerRepository;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class FeedbackService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private TrainerRepository $trainerRepository;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, TrainerRepository $trainerRepository, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->trainerRepository = $trainerRepository;
        $this->requestChecker = $requestChecker;
    }

    public function createFeedback(int $userID, int $trainerID, int $rating, string $comment): Feedback
    {
        $this->requestChecker->check([
            'userID' => $userID,
            'trainerID' => $trainerID,
            'rating' => $rating,
            'comment' => $comment
        ], ['userID', 'trainerID', 'rating', 'comment']);

        $this->requestChecker->validateRequestDataByConstraints($rating, [
            'rating' => [
                new Assert\NotBlank(),
                new Assert\Range(['min' => 1, 'max' => 5])
            ]
        ]);

        $this->requestChecker->validateRequestDataByConstraints($comment, [
            'comment' => [
                new Assert\NotBlank()
            ]
        ]);

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
            ->setTrainer($trainer)
            ->setRating($rating)
            ->setComment($comment);

            $this->entityManager->persist($feedback);
            $this->entityManager->flush();

            return $feedback;  
        }

    }
}
