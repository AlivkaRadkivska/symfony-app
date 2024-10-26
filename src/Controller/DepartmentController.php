<?php

namespace App\Controller;

use App\Services\Department\DepartmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/department', name: 'department_routes')]
class DepartmentController extends AbstractController
{
    /**
     * @var DepartmentService
     */
    private DepartmentService $departmentService;

    /**
     * __construct
     *
     * @param  DepartmentService $departmentService
     */
    public function __construct(
        DepartmentService $departmentService
    ) {
        $this->departmentService = $departmentService;
    }

    /**
     * getDepartments
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_departments')]
    public function getDepartments(): JsonResponse
    {
        $departments = $this->departmentService->getDepartments();
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

        return new JsonResponse($department, Response::HTTP_OK);
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

        return new JsonResponse($department, Response::HTTP_OK);
    }
}
