<?php

namespace App\Controller;

use App\Services\Teacher\TeacherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\RequestCheckerService;

#[Route('/teacher', name: 'teacher_routes')]
class TeacherController extends AbstractController
{
    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestChecker;

    /**
     * @var TeacherService
     */
    private TeacherService $teacherService;

    /**
     * @var array
     */
    public const REQUIRED_TEACHER_FIELDS = [
        'email',
        'password',
        'firstName',
        'lastName',
        'position',
        'departmentId'
    ];

    /**
     * __construct
     *
     * @param  RequestCheckerService $requestChecker 
     * @param  TeacherService $teacherService 
     * @return void
     */
    public function __construct(
        RequestCheckerService $requestChecker,
        TeacherService $teacherService
    ) {
        $this->requestChecker = $requestChecker;
        $this->teacherService = $teacherService;
    }

    /**
     * getTeachers
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_teachers', methods: ['GET'])]
    public function getTeachers(): JsonResponse
    {
        $teachers = $this->teacherService->getTeachers();
        return new JsonResponse($teachers, Response::HTTP_OK);
    }

    /**
     * addTeacher
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_teacher', methods: ['POST'])]
    public function addTeacher(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestChecker::check($requestData, self::REQUIRED_TEACHER_FIELDS);

        $teacher = $this->teacherService->createTeacher($requestData);


        return new JsonResponse($teacher, Response::HTTP_OK);
    }

    /**
     * getTeacher
     *
     * @param  string $id 
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_teacher')]
    public function getTeacher(string $id): JsonResponse
    {
        $teacher = $this->teacherService->getTeacher($id);
        return new JsonResponse($teacher, Response::HTTP_OK);
    }

    /**
     * deleteTeacher
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_teacher', methods: ['DELETE'])]
    public function deleteTeacher(string $id): JsonResponse
    {
        $this->teacherService->deleteTeacher($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * updateTeacher
     *
     * @param  string $id 
     * @param  Request $request 
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_teacher', methods: ['PATCH'])]
    public function updateTeacher(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $teacher = $this->teacherService->updateTeacher($id, $requestData);

        return new JsonResponse($teacher, Response::HTTP_OK);
    }
}
