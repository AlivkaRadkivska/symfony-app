<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/tests')]
class TestController extends AbstractController
{
    #[Route('/reset', name: 'reset_tests', methods: ['GET'])]
    public function resetTests(SessionInterface $session): JsonResponse
    {
        $session->set('testItems', [
            ['id' => 0, 'name' => 'Item 1', 'desc' => 'Something is not here'],
            ['id' => 1, 'name' => 'Ittem 2', 'desc' => 'Something is here'],
            ['id' => 2, 'name' => 'Ittem 3', 'desc' => 'Something ig']
        ]);

        return new JsonResponse($session->get('testItems'), 200);
    }

    #[Route('/', name: 'get_tests', methods: ['GET'])]
    public function getTests(Request $request, SessionInterface $session): JsonResponse
    {
        $reqParams = $request->query->all();
        $testItems = $session->get('testItems');

        $res = (array_key_exists('search', $reqParams)) ? array_filter(
            $testItems,
            fn($item) =>
            strpos($item['name'], $reqParams['search']) !== false
        ) : $testItems;


        return new JsonResponse($res, 200);
    }

    #[Route('/{id}', name: 'get_test', methods: ['GET'])]
    public function getTest(string $id, SessionInterface $session): JsonResponse
    {
        $testItems = $session->get('testItems');

        foreach ($testItems as $item)
            if ($item['id'] == $id)
                return new JsonResponse($item, 200);

        return new JsonResponse(['message' => 'Item not found'], 404);
    }

    #[Route('/add', name: 'add_test', methods: ['POST'])]
    public function addTest(Request $request, SessionInterface $session): JsonResponse
    {
        $reqBody = json_decode($request->getContent(), true);

        if (!isset($reqBody['id'], $reqBody['name'], $reqBody['desc'])) {
            return new JsonResponse(['message' => 'There are missing fields'], 400);
        }

        $testItems = $session->get('testItems', []);
        $testItems[] = $reqBody;
        $session->set('testItems', $testItems);

        return new JsonResponse($reqBody, 201);
    }

    #[Route('/update/{id}', name: 'update_test', methods: ['PATCH'])]
    public function updateTest(string $id, Request $request, SessionInterface $session): JsonResponse
    {
        $testItems = $session->get('testItems');

        foreach ($testItems as $i => $item)
            if ($item['id'] == $id) {
                $reqBody = json_decode($request->getContent(), true);
                $filteredData = array_intersect_key($reqBody, array_flip(['id', 'name', 'desc']));

                $testItems[$i] = array_merge($item, $filteredData);
                $session->set('testItems', $testItems);

                return new JsonResponse($testItems[$i], 200);
            }

        return new JsonResponse(['message' => 'Item not found'], 404);
    }

    #[Route('/delete/{id}', name: 'delete_test', methods: ['DELETE'])]
    public function deleteTest(string $id, SessionInterface $session): JsonResponse
    {
        $testItems = $session->get('testItems');

        foreach ($testItems as $i => $item)
            if ($item['id'] == $id) {
                unset($testItems[$i]);
                $session->set('testItems', $testItems);

                return new JsonResponse([], 204);
            }

        return new JsonResponse(['message' => 'Item not found'], 404);
    }
}
