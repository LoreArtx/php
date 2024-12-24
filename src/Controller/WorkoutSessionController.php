<?php

namespace App\Controller;

use App\Entity\WorkoutSession;
use App\Form\WorkoutSessionType;
use App\Service\WorkoutSessionService;
use App\Service\WorkoutSessionValidatorService;
use App\Repository\WorkoutSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/workout/session')]
final class WorkoutSessionController extends AbstractController
{
    private WorkoutSessionService $workoutSessionService;
    private WorkoutSessionValidatorService $workoutSessionValidatorService;
    public function __construct(WorkoutSessionService $workoutSessionService, WorkoutSessionValidatorService $workoutSessionValidatorService){
        $this->workoutSessionService = $workoutSessionService;
        $this->workoutSessionValidatorService = $workoutSessionValidatorService;
    }

    #[Route(name: 'app_workout_session_index', methods: ['GET'])]
    public function index(Request $request, WorkoutSessionRepository $workoutSessionRepository): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = (int)(isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : 1);
        $page = (int)$request->query->get('page', 1);

        $workoutSessionData = $workoutSessionRepository->getWorkoutSessionsPaginated($itemsPerPage, $page);

        return $this->render('workout_session/index.html.twig', [
            'workout_sessions' => $workoutSessionData['workoutSessions'],
            'totalItems' => $workoutSessionData['totalItems'],
            'totalPages' => $workoutSessionData['totalPages'],
            'currentPage' => $page,
        ]);
    }

    #[Route('/new', name: 'app_workout_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workoutSession = new WorkoutSession();
        $form = $this->createForm(WorkoutSessionType::class, $workoutSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->workoutSessionValidatorService->validateWorkoutSession([
                'program' => $workoutSession->getProgram()->getId(),
                'trainer' => $workoutSession->getTrainer()->getId(),
                'start_time' => $workoutSession->getStartTime(),
                'end_time' => $workoutSession->getEndTime(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }
            
            if($form->isValid()){
                $this->workoutSessionService->createWorkoutSession(
                    $workoutSession->getProgram()->getId(),
                    $workoutSession->getTrainer()->getId(),
                    $workoutSession->getStartTime(),
                    $workoutSession->getEndTime()
                );

                return $this->redirectToRoute('app_workout_session_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('workout_session/new.html.twig', [
            'workout_session' => $workoutSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_session_show', methods: ['GET'])]
    public function show(WorkoutSession $workoutSession): Response
    {
        return $this->render('workout_session/show.html.twig', [
            'workout_session' => $workoutSession,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workout_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkoutSession $workoutSession, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkoutSessionType::class, $workoutSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_workout_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout_session/edit.html.twig', [
            'workout_session' => $workoutSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_session_delete', methods: ['POST'])]
    public function delete(Request $request, WorkoutSession $workoutSession, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workoutSession->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($workoutSession);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workout_session_index', [], Response::HTTP_SEE_OTHER);
    }
}
