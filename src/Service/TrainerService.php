<?php

namespace App\Service;

use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TrainerService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->requestChecker = $requestChecker;
    }

    public function createTrainer(string $name, string $specialization, int $experience, string $email, string $phone): Trainer
    {
        $content = ['name' => $name, 'specialization' => $specialization, 'experience' => $experience, 'email' => $email, 'phone' => $phone];
        try {
            $this->requestChecker->check($content, ['name', 'specialization', 'experience', 'email', 'phone']);
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException('Missing required fields: ' . $e->getMessage());
        }

        try {
            $this->requestChecker->validateRequestDataByConstraints($content, [
                'name' => [new Assert\NotBlank()],
                'specialization' => [new Assert\NotBlank()],
                'experience' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'phone' => [new Assert\NotBlank()],
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException $e) {
            throw new BadRequestHttpException('Validation failed: ' . $e->getMessage());
        }


        $trainer = new Trainer();

        $trainer->setName($name)
            ->setSpecialization($specialization)
            ->setExperience($experience)
            ->setEmail($email)
            ->setPhone($phone);

        $this->entityManager->persist($trainer);
        $this->entityManager->flush();

        return $trainer;
    }
}
