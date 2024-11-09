<?php

namespace App\Controller;

use App\Services\Task\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task', name: 'task_routes')]
class TaskController extends AbstractController
{
    /**
     * @var TaskService
     */
    private TaskService $taskService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * __construct
     *
     * @param  TaskService $taskService
     */
    public function __construct(
        TaskService $taskService,
        EntityManagerInterface $entityManager
    ) {
        $this->taskService = $taskService;
        $this->entityManager = $entityManager;
    }

    /**
     * getTasks
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'get_tasks')]
    public function getTasks(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $tasks = $this->taskService->getTasks($requestData);

        return new JsonResponse($tasks, Response::HTTP_OK);
    }

    /**
     * addTask
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_task', methods: ['POST'])]
    public function addTask(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $task = $this->taskService->createTask($requestData);
        $this->entityManager->flush();

        return new JsonResponse($task, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get_task')]
    /**
     * getTask
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function getTask(string $id): JsonResponse
    {
        $tasks = $this->taskService->getTask($id);
        return new JsonResponse($tasks, Response::HTTP_OK);
    }

    /**
     * deleteTask
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_task', methods: ['DELETE'])]
    public function deleteTask(string $id): JsonResponse
    {
        $this->taskService->deleteTask($id);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateTask
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_task', methods: ['PATCH'])]
    public function updateTask(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $task = $this->taskService->updateTask($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($task, Response::HTTP_OK);
    }
}
