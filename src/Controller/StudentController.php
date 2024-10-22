<?php

namespace App\Controller;

use App\Services\Student\StudentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\RequestCheckerService;

#[Route('/student', name: 'student_routes')]
class StudentController extends AbstractController
{
    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestChecker;

    /**
     * @var StudentService
     */
    private StudentService $studentService;

    /**
     * @var array
     */
    public const REQUIRED_STUDENT_FIELDS = [
        'email',
        'password',
        'firstName',
        'lastName',
        'groupId'
    ];

    /**
     * __construct
     *
     * @param  RequestCheckerService $requestChecker 
     * @param  StudentService $studentService 
     * @return void
     */
    public function __construct(
        RequestCheckerService $requestChecker,
        StudentService $studentService
    ) {
        $this->requestChecker = $requestChecker;
        $this->studentService = $studentService;
    }

    /**
     * getStudents
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_students', methods: ['GET'])]
    public function getStudents(): JsonResponse
    {
        $students = $this->studentService->getStudents();
        return new JsonResponse($students, Response::HTTP_OK);
    }

    /**
     * addStudent
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_student', methods: ['POST'])]
    public function addStudent(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestChecker::check($requestData, self::REQUIRED_STUDENT_FIELDS);

        $student = $this->studentService->createStudent($requestData);


        return new JsonResponse($student, Response::HTTP_OK);
    }

    /**
     * getStudent
     *
     * @param  string $id 
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_student')]
    public function getStudent(string $id): JsonResponse
    {
        $student = $this->studentService->getStudent($id);
        return new JsonResponse($student, Response::HTTP_OK);
    }

    /**
     * deleteStudent
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_student', methods: ['DELETE'])]
    public function deleteStudent(string $id): JsonResponse
    {
        $this->studentService->deleteStudent($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * updateStudent
     *
     * @param  string $id 
     * @param  Request $request 
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_student', methods: ['PATCH'])]
    public function updateStudent(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $student = $this->studentService->updateStudent($id, $requestData);

        return new JsonResponse($student, Response::HTTP_OK);
    }

    /**
     * joinCourse
     *
     * @param  string $id
     * @param  string $courseId
     * @return JsonResponse
     */
    #[Route('/{id}/join-course/{courseId}', name: 'update_student_join_course', methods: ['GET'])]
    public function joinCourse(string $id, string $courseId): JsonResponse
    {
        $student = $this->studentService->joinCourse($id, $courseId);

        return new JsonResponse($student, Response::HTTP_OK);
    }

    /**
     * leaveCourse
     *
     * @param  string $id
     * @param  string $courseId
     * @return JsonResponse
     */
    #[Route('/{id}/leave-course/{courseId}', name: 'update_student_leave_course', methods: ['GET'])]
    public function leaveCourse(string $id, string $courseId): JsonResponse
    {
        $student = $this->studentService->leaveCourse($id, $courseId);

        return new JsonResponse($student, Response::HTTP_OK);
    }
}
