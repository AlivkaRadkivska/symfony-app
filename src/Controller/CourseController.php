<?php

namespace App\Controller;

use App\Services\Course\CourseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\RequestCheckerService;

#[Route('/course', name: 'course_routes')]
class CourseController extends AbstractController
{
    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestChecker;

    /**
     * @var CourseService
     */
    private CourseService $courseService;

    /**
     * @var array
     */
    public const REQUIRED_COURSE_FIELDS = [
        'name',
        'description',
        'credits',
        'teacherId'
    ];

    /**
     * __construct
     *
     * @param  RequestCheckerService $requestChecker
     * @param  CourseService $courseService
     * @return void
     */
    public function __construct(
        RequestCheckerService $requestChecker,
        CourseService $courseService
    ) {
        $this->requestChecker = $requestChecker;
        $this->courseService = $courseService;
    }

    /**
     * getCourses
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_courses')]
    public function getCourses(): JsonResponse
    {
        $courses = $this->courseService->getCourses();
        return new JsonResponse($courses, Response::HTTP_OK);
    }

    /**
     * addCourse
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_course', methods: ['POST'])]
    public function addCourse(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestChecker::check($requestData, self::REQUIRED_COURSE_FIELDS);

        $course = $this->courseService->createCourse($requestData);

        return new JsonResponse($course, Response::HTTP_OK);
    }

    /**
     * deleteCourse
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_course', methods: ['DELETE'])]
    public function deleteCourse(string $id): JsonResponse
    {
        $this->courseService->deleteCourse($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateCourse
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_course', methods: ['PATCH'])]
    public function updateCourse(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $course = $this->courseService->updateCourse($id, $requestData);

        return new JsonResponse($course, Response::HTTP_OK);
    }
}
