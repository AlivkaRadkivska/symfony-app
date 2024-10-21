<?php

namespace App\Controller;

use App\Services\Department\DepartmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\RequestCheckerService;

#[Route('/department', name: 'department_routes')]
class DepartmentController extends AbstractController
{
    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestChecker;

    /**
     * @var RequestCheckerService
     */
    private DepartmentService $departmentService;

    /**
     * @var array
     */
    public const REQUIRED_DEPARTMENT_FIELDS = [
        'name',
        'faculty'
    ];


    /**
     * __construct
     *
     * @param  RequestCheckerService $requestChecker
     * @param  DepartmentService $departmentService
     * @return void
     */
    public function __construct(
        RequestCheckerService $requestChecker,
        DepartmentService $departmentService
    ) {
        $this->requestChecker = $requestChecker;
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
        $this->requestChecker::check($requestData, self::REQUIRED_DEPARTMENT_FIELDS);

        $department = $this->departmentService->createDepartment($requestData['name'], $requestData['faculty']);


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
