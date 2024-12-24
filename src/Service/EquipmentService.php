<?php

namespace App\Service;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class EquipmentService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->requestChecker = $requestChecker;

    }

    public function createEquipment(string $name, string $type, int $quantity, string $status): Equipment
    {
        $this->requestChecker->check([
            'name' => $name,
            'type' => $type,
            'quantity' => $quantity,
            'status' => $status
        ], ['name', 'type', 'quantity', 'status']);

        $this->requestChecker->validateRequestDataByConstraints($status, [
            'status' => [
                new Assert\NotBlank(),
                new Assert\Choice(['values' => ['available', 'unavailable', 'in_use']])
            ]
        ]);

        $this->requestChecker->validateRequestDataByConstraints($quantity, [
            'quantity' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'integer'])
            ]
        ]);


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
