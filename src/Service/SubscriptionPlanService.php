<?php

namespace App\Service;

use App\Entity\SubscriptionPlan;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionPlanService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSubscriptionPlan(string $name, string $price, int $duration): SubscriptionPlan
    {
        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($name)
                         ->setPrice($price)
                         ->setDuration($duration);

        $this->entityManager->persist($subscriptionPlan);
        $this->entityManager->flush();

        return $subscriptionPlan;
    }
}
