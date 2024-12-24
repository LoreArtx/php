<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->requestChecker = $requestChecker;
    }

    public function createUser(string $name, string $email, string $password, string $phone, string $role): User
    {
        $content = ['name' => $name, 'email' => $email, 'password' => $password, 'phone' => $phone, 'role' => $role];
        
        try {
            $this->requestChecker->check($content, ['name', 'email', 'password', 'phone', 'role']);
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException('Missing required fields: ' . $e->getMessage());
        }

        try {
            $this->requestChecker->validateRequestDataByConstraints($content, [
                'name' => [new Assert\NotBlank()],
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 6])],
                'phone' => [new Assert\NotBlank(), new Assert\Regex('/^\+?[1-9]\d{1,14}$/')],
                'role' => [new Assert\NotBlank()],
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException $e) {
            throw new BadRequestHttpException('Validation failed: ' . $e->getMessage());
        }

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
