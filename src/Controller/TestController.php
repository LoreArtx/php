<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

 #[Route('/test')]
class TestController extends AbstractController
{
    #[Route('', name: 'app_test_get', methods:["GET"])]
    public function get(Request $request): JsonResponse
    {
        $querryParams = $request->query->all(); 
        return new JsonResponse($querryParams);
    }

    #[Route('/post', name:'app_test_post', methods:["POST"])]
    public function post(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        return new JsonReponse($requestBody);
    }


    #[Route('/getSomething', name:'app_test_get_something', methods:["GET"])]
    public function getItem(Request $request) : JsonResponse
    {
        return new JsonResponse();
    }

    #[Route('/helloWorld', name:'app_test_hello_world', methods:["GET"])]
    public function getHelloWorld() : JsonResponse
    {
        return new JsonResponse("Hello World!");
    }
}
