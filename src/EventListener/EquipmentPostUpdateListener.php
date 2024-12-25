<?php

namespace App\EventListener;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class EquipmentPostUpdateListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postUpdate(Equipment $equipment, PostUpdateEventArgs $args): void
    {
        $changeSet = $args->getEntityChangeSet();

        if (isset($changeSet['quantity'])) {
            $newQuantity = $changeSet['quantity'][1];

            if ($newQuantity <= 0) {
                $equipment->setStatus('unavailable');
                $this->entityManager->flush();
            }
        }
    }
}
