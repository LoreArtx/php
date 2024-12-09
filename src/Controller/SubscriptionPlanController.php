<?php

namespace App\Controller;

use App\Entity\SubscriptionPlan;
use App\Form\SubscriptionPlanType;
use App\Service\SubscriptionPlanService;
use App\Service\SubscriptionPlanValidatorService;
use App\Repository\SubscriptionPlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/subscription/plan')]
final class SubscriptionPlanController extends AbstractController
{
    private SubscriptionPlanService $subscriptionPlanService;
    private SubscriptionPlanValidatorService $subscriptionPlanValidatorService;

    public function __construct(SubscriptionPlanService $subscriptionPlanService, SubscriptionPlanValidatorService $subscriptionPlanValidatorService)
    {
        $this->subscriptionPlanService = $subscriptionPlanService;
        $this->subscriptionPlanValidatorService = $subscriptionPlanValidatorService;
    }

    #[Route(name: 'app_subscription_plan_index', methods: ['GET'])]
    public function index(SubscriptionPlanRepository $subscriptionPlanRepository): Response
    {
        return $this->render('subscription_plan/index.html.twig', [
            'subscription_plans' => $subscriptionPlanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_subscription_plan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subscriptionPlan = new SubscriptionPlan();
        $form = $this->createForm(SubscriptionPlanType::class, $subscriptionPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->subscriptionPlanValidatorService->validateSubscriptionPlanData([
                'name' => $subscriptionPlan->getName(),
                'price' => $subscriptionPlan->getPrice(),
                'duration' => $subscriptionPlan->getDuration(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }
            
            if($form->isValid())
            {
                $this->subscriptionPlanService->createSubscriptionPlan(
                $subscriptionPlan->getName(),
                $subscriptionPlan->getPrice(),
                $subscriptionPlan->getDuration() );
                return $this->redirectToRoute('app_subscription_plan_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('subscription_plan/new.html.twig', [
            'subscription_plan' => $subscriptionPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subscription_plan_show', methods: ['GET'])]
    public function show(SubscriptionPlan $subscriptionPlan): Response
    {
        return $this->render('subscription_plan/show.html.twig', [
            'subscription_plan' => $subscriptionPlan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subscription_plan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubscriptionPlan $subscriptionPlan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubscriptionPlanType::class, $subscriptionPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subscription_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subscription_plan/edit.html.twig', [
            'subscription_plan' => $subscriptionPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subscription_plan_delete', methods: ['POST'])]
    public function delete(Request $request, SubscriptionPlan $subscriptionPlan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subscriptionPlan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($subscriptionPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subscription_plan_index', [], Response::HTTP_SEE_OTHER);
    }
}
