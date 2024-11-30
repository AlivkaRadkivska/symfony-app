<?php

namespace App\Controller;

use App\Services\ExamResult\ExamResultService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exam-result', name: 'exam_result_routes')]
class ExamResultController extends AbstractController
{
    /**
     * @var ExamResultService
     */
    private ExamResultService $examResultService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * __construct
     *
     * @param  ExamResultService $ExamResultService
     */
    public function __construct(
        ExamResultService $examResultService,
        EntityManagerInterface $entityManager
    ) {
        $this->examResultService = $examResultService;
        $this->entityManager = $entityManager;
    }

    /**
     * getExamResults
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'get_exam_results')]
    public function getExamResults(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $examResults = $this->examResultService->getExamResults($requestData);

        return new JsonResponse($examResults, Response::HTTP_OK);
    }

    /**
     * addExamResult
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_exam_result', methods: ['POST'])]
    public function addExamResult(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $examResult = $this->examResultService->createExamResult($requestData);
        $this->entityManager->flush();

        return new JsonResponse($examResult, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get_exam_result')]
    /**
     * getExamResult
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function getExamResult(string $id): JsonResponse
    {
        $examResults = $this->examResultService->getExamResult($id);
        return new JsonResponse($examResults, Response::HTTP_OK);
    }

    /**
     * deleteExamResult
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_exam_result', methods: ['DELETE'])]
    public function deleteExamResult(string $id): JsonResponse
    {
        $this->examResultService->deleteExamResult($id);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateExamResult
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_exam_result', methods: ['PATCH'])]
    public function updateExamResult(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $examResult = $this->examResultService->updateExamResult($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($examResult, Response::HTTP_OK);
    }
}
