<?php

namespace App\Action;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateEquipmentStatusAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
     * @param int $id
     * @return JsonResponse
     */
    public function __invoke(int $id): JsonResponse
    {
        $equipment = $this->entityManager->getRepository(Equipment::class)->find($id);

        if (!$equipment) {
            throw new NotFoundHttpException('Equipment not found.');
        }

        if ($equipment->getStatus() === 'unavailable') {
            throw new BadRequestHttpException('This equipment is already unavailable.');
        }

        $equipment->setStatus('unavailable');
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'equipment' => $equipment], 200);
    }
}
