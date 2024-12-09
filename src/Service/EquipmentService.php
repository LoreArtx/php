<?php

namespace App\Service;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;

class EquipmentService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createEquipment(string $name, string $type, int $quantity, string $status): Equipment
    {
        $equipment = new Equipment();
        $equipment->setName($name)
                 ->setType($type)
                 ->setQuantity($quantity)
                 ->setStatus($status);

        $this->entityManager->persist($equipment);
        $this->entityManager->flush();

        return $equipment;
    }
}
