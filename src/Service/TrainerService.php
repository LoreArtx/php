<?php

namespace App\Service;

use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;

class TrainerService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTrainer(string $name, string $specialization, int $experience, string $email, string $phone): Trainer
    {
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
