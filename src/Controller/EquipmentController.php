<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Form\EquipmentType;
use App\Service\EquipmentService;
use App\Service\EquipmentValidatorService;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/equipment')]
final class EquipmentController extends AbstractController
{
    private EquipmentService $equipmentService;
    private EquipmentValidatorService $equipmentValidator;

    public function __construct(EquipmentService $equipmentService, EquipmentValidatorService $equipmentValidator)
    {
        $this->equipmentService = $equipmentService;
        $this->equipmentValidator = $equipmentValidator;
    }
    
    #[Route(name: 'app_equipment_index', methods: ['GET'])]
    public function index(Request $request, EquipmentRepository $equipmentRepository): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = (int)(isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : 1);
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $equipmentData = $equipmentRepository->getAllEquipmentByFilter($requestData, $itemsPerPage, $page);

        return $this->render('equipment/index.html.twig', [
            'equipment' => $equipmentData['equipment'],
            'totalItems' => $equipmentData['totalItems'],
            'totalPages' => $equipmentData['totalPages'],
            'currentPage' => $page,
        ]);
    }

    #[Route('/new', name: 'app_equipment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->equipmentValidator->validateEquipment([
                'name'=>$equipment->getName(),
                'type' => $equipment->getType(),
                'quantity' => $equipment->getQuantity(),
                'status' => $equipment->getStatus(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if($form->isValid()){
                $this->equipmentService->createEquipment(
                $equipment->getName(),
                $equipment->getType(),
                $equipment->getQuantity(),
                $equipment->getStatus());

                return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->render('equipment/new.html.twig', [
            'equipment' => $equipment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipment_show', methods: ['GET'])]
    public function show(Equipment $equipment): Response
    {
        return $this->render('equipment/show.html.twig', [
            'equipment' => $equipment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipment/edit.html.twig', [
            'equipment' => $equipment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipment_delete', methods: ['POST'])]
    public function delete(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($equipment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
    }
}
