<?php

namespace App\Controller;

use App\Service\FeedbackService;
use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Service\FeedbackValidatorService;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/feedback')]
final class FeedbackController extends AbstractController
{
    private FeedbackService $feedbackService;
    private FeedbackValidatorService $feedbackValidatorService;
    public function __construct(FeedbackService $feedbackService, FeedbackValidatorService $feedbackValidatorService)
    {
        $this->feedbackService = $feedbackService;
        $this->feedbackValidatorService = $feedbackValidatorService;

    }

    #[Route(name: 'app_feedback_index', methods: ['GET'])]
    public function index(Request $request, FeedbackRepository $feedbackRepository): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = (int)(isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : 1);
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $feedbackData = $feedbackRepository->getAllFeedbackByFilter($requestData, $itemsPerPage, $page);

        return $this->render('feedback/index.html.twig', [
            'feedback' => $feedbackData['feedback'],
            'totalItems' => $feedbackData['totalItems'],
            'totalPages' => $feedbackData['totalPages'],
            'currentPage' => $page,
        ]);
    }

    #[Route('/new', name: 'app_feedback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->feedbackValidatorService->validateFeedbackData([
                'user'=>$feedback->getUser()->getId(),
                'trainer' => $feedback->getTrainer()->getId(),
                'rating' => $feedback->getRating(),
                'comment' => $feedback->getComment(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if($form->isValid())
            {
                $this->feedbackService->createFeedback(
                    $feedback->getUser()->getId(),
                    $feedback->getTrainer()->getId(),
                    $feedback->getRating(),
                    $feedback->getComment()
                );

                return $this->redirectToRoute('app_feedback_index');
            }

        }

        return $this->render('feedback/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feedback_show', methods: ['GET'])]
    public function show(Feedback $feedback): Response
    {
        return $this->render('feedback/show.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feedback/edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feedback_delete', methods: ['POST'])]
    public function delete(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feedback_index', [], Response::HTTP_SEE_OTHER);
    }
}
