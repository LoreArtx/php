<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test', methods:["POST"])]
    public function index(Request $request): JsonResponse
    {
        $querryParams = $request->query->all();
        dd($querryParams); 
        return new JsonResponse();
    }
}
