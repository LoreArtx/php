<?php

namespace App\Controller;

use App\Entity\WorkoutProgram;
use App\Service\WorkoutProgramService;
use App\Form\WorkoutProgramType;
use App\Service\WorkoutProgramValidatorService;
use App\Repository\WorkoutProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/workout/program')]
final class WorkoutProgramController extends AbstractController
{
    private WorkoutProgramService $workoutProgramService;
    private WorkoutProgramValidatorService $workoutProgramValidatorService;

    public function __construct(WorkoutProgramService $workoutProgramService, WorkoutProgramValidatorService $workoutProgramValidatorService)
    {
        $this->workoutProgramService = $workoutProgramService;
        $this->workoutProgramValidatorService = $workoutProgramValidatorService;
    }

    #[Route(name: 'app_workout_program_index', methods: ['GET'])]
    public function index(WorkoutProgramRepository $workoutProgramRepository): Response
    {
        return $this->render('workout_program/index.html.twig', [
            'workout_programs' => $workoutProgramRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_workout_program_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workoutProgram = new WorkoutProgram();
        $form = $this->createForm(WorkoutProgramType::class, $workoutProgram);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $validationErrors = $this->workoutProgramValidatorService->validateWorkoutProgramData([
                'name' => $workoutProgram->getName(),
                'description' => $workoutProgram->getDescription(),
                'duration' => $workoutProgram->getDuration(),
                'trainer' => $workoutProgram->getTrainer()->getId(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }
            
            if($form->isValid())
            {
                $this->workoutProgramService->createWorkoutProgram(
                    $workoutProgram->getName(),
                    $workoutProgram->getDescription(),
                    $workoutProgram->getDuration(),
                    $workoutProgram->getTrainer()->getId()
                );

                return $this->redirectToRoute('app_workout_program_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('workout_program/new.html.twig', [
            'workout_program' => $workoutProgram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_program_show', methods: ['GET'])]
    public function show(WorkoutProgram $workoutProgram): Response
    {
        return $this->render('workout_program/show.html.twig', [
            'workout_program' => $workoutProgram,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workout_program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkoutProgram $workoutProgram, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkoutProgramType::class, $workoutProgram);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_workout_program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout_program/edit.html.twig', [
            'workout_program' => $workoutProgram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_program_delete', methods: ['POST'])]
    public function delete(Request $request, WorkoutProgram $workoutProgram, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workoutProgram->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($workoutProgram);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workout_program_index', [], Response::HTTP_SEE_OTHER);
    }
}
