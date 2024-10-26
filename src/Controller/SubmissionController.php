<?php

namespace App\Controller;

use App\Services\Submission\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/submission', name: 'submission_routes')]
class SubmissionController extends AbstractController
{
    /**
     * @var SubmissionService
     */
    private SubmissionService $submissionService;


    /**
     * __construct
     *
     * @param  SubmissionService $SubmissionService
     */
    public function __construct(
        SubmissionService $submissionService
    ) {
        $this->submissionService = $submissionService;
    }

    /**
     * getSubmissions
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_submissions')]
    public function getSubmissions(): JsonResponse
    {
        $submissions = $this->submissionService->getSubmissions();
        return new JsonResponse($submissions, Response::HTTP_OK);
    }

    /**
     * addSubmission
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_submission', methods: ['POST'])]
    public function addSubmission(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $submission = $this->submissionService->createSubmission($requestData);
        return new JsonResponse($submission, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_submission')]
    /**
     * getSubmission
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function getSubmission(string $id): JsonResponse
    {
        $submissions = $this->submissionService->getSubmission($id);
        return new JsonResponse($submissions, Response::HTTP_OK);
    }

    /**
     * deleteSubmission
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_submission', methods: ['DELETE'])]
    public function deleteSubmission(string $id): JsonResponse
    {
        $this->submissionService->deleteSubmission($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateSubmission
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_submission', methods: ['PATCH'])]
    public function updateSubmission(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $submission = $this->submissionService->updateSubmission($id, $requestData);
        return new JsonResponse($submission, Response::HTTP_OK);
    }
}