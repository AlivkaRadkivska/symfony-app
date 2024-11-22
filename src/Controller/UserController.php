<?php

namespace App\Controller;

use App\Services\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_routes')]
class UserController extends AbstractController
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * __construct
     *
     * @param  UserService $userService 
     */
    public function __construct(
        UserService $userService,
        EntityManagerInterface $entityManager
    ) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
    }

    /**
     * getUsers
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'get_users', methods: ['GET'])]
    public function getUsers(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $users = $this->userService->getUsers($requestData);

        return new JsonResponse($users, Response::HTTP_OK);
    }

    /**
     * addUser
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_user', methods: ['POST'])]
    public function addUser(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $user = $this->userService->createUser($requestData);
        $this->entityManager->flush();

        return new JsonResponse($user, Response::HTTP_CREATED);
    }

    /**
     * getUser
     *
     * @param  string $id 
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_user')]
    public function getOneUser(string $id): JsonResponse
    {
        $user = $this->userService->getUser($id);
        return new JsonResponse($user, Response::HTTP_OK);
    }

    /**
     * deleteUser
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_user', methods: ['DELETE'])]
    public function deleteUser(string $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateUser
     *
     * @param  string $id 
     * @param  Request $request 
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_user', methods: ['PATCH'])]
    public function updateUser(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $user = $this->userService->updateUser($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($user, Response::HTTP_OK);
    }
}
