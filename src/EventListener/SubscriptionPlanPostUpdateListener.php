<?php

namespace App\EventListener;

use App\Entity\SubscriptionPlan;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class SubscriptionPlanPostUpdateListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postUpdate(SubscriptionPlan $subscriptionPlan, PostUpdateEventArgs $args): void
    {
        $changeSet = $args->getEntityChangeSet();

        if (isset($changeSet['price'])) {
            $oldPrice = $changeSet['price'][0];
            $newPrice = $changeSet['price'][1];
        }
    }
}
