<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createUser(string $name, string $email, string $password, string $phone, string $role): User
    {
        $user = new User();

        $user->setName($name)
            ->setEmail($email)
            ->setPassword($password)
            ->setPhone($phone)
            ->setRole($role);



        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
