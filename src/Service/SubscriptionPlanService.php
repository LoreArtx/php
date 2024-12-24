<?php

namespace App\Service;

use App\Entity\SubscriptionPlan;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionPlanService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->requestChecker = $requestChecker;
    }

    public function createSubscriptionPlan(string $name, string $price, int $duration): SubscriptionPlan
    {
        $content = ['name' => $name, 'price' => $price, 'duration' => $duration];
        try {
            $this->requestChecker->check($content, ['name', 'price', 'duration']);
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException('Missing required fields: ' . $e->getMessage());
        }

        try {
            $this->requestChecker->validateRequestDataByConstraints($content, [
                'name' => [new Assert\NotBlank()],
                'price' => [new Assert\NotBlank(), new Assert\Type('string')],
                'duration' => [new Assert\NotBlank(), new Assert\Type('integer')],
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException $e) {
            throw new BadRequestHttpException('Validation failed: ' . $e->getMessage());
        }

        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($name)
                         ->setPrice($price)
                         ->setDuration($duration);

        $this->entityManager->persist($subscriptionPlan);
        $this->entityManager->flush();

        return $subscriptionPlan;
    }
}
