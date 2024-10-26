<?php

namespace App\Controller;

use App\Services\Exam\ExamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exam', name: 'exam_routes')]
class ExamController extends AbstractController
{
    /**
     * @var ExamService
     */
    private ExamService $examService;

    /**
     * __construct
     *
     * @param  ExamService $ExamService
     */
    public function __construct(
        ExamService $examService
    ) {
        $this->examService = $examService;
    }

    /**
     * getExams
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_exams')]
    public function getExams(): JsonResponse
    {
        $exams = $this->examService->getExams();
        return new JsonResponse($exams, Response::HTTP_OK);
    }

    /**
     * addExam
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_exam', methods: ['POST'])]
    public function addExam(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $exam = $this->examService->createExam($requestData);
        return new JsonResponse($exam, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_exam')]
    /**
     * getExam
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function getExam(string $id): JsonResponse
    {
        $exams = $this->examService->getExam($id);
        return new JsonResponse($exams, Response::HTTP_OK);
    }

    /**
     * deleteExam
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_exam', methods: ['DELETE'])]
    public function deleteExam(string $id): JsonResponse
    {
        $this->examService->deleteExam($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateExam
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_exam', methods: ['PATCH'])]
    public function updateExam(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $exam = $this->examService->updateExam($id, $requestData);
        return new JsonResponse($exam, Response::HTTP_OK);
    }
}
