<?php

namespace App\Controller;

use App\Services\Department\DepartmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/department', name: 'department_routes')]
class DepartmentController extends AbstractController
{
    /**
     * @var DepartmentService
     */
    private DepartmentService $departmentService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * __construct
     *
     * @param  DepartmentService $departmentService
     */
    public function __construct(
        DepartmentService $departmentService,
        EntityManagerInterface $entityManager
    ) {
        $this->departmentService = $departmentService;
        $this->entityManager = $entityManager;
    }

    /**
     * getDepartments
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'get_departments')]
    public function getDepartments(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $departments = $this->departmentService->getDepartments($requestData);

        return new JsonResponse($departments, Response::HTTP_OK);
    }

    /**
     * addDepartment
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_department', methods: ['POST'])]
    public function addDepartment(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $department = $this->departmentService->createDepartment($requestData);
        $this->entityManager->flush();

        return new JsonResponse($department, Response::HTTP_CREATED);
    }

    /**
     * deleteDepartment
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_department', methods: ['DELETE'])]
    public function deleteDepartment(string $id): JsonResponse
    {
        $this->departmentService->deleteDepartment($id);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateDepartment
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_department', methods: ['PATCH'])]
    public function updateDepartment(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $department = $this->departmentService->updateDepartment($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($department, Response::HTTP_OK);
    }
}
