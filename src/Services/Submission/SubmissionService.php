<?php

namespace App\Services\Submission;

use App\Entity\Submission;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\Task\TaskService;
use App\Services\Student\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class SubmissionService
{
  /**
   * @var array
   */
  public const REQUIRED_SUBMISSION_FIELDS = [
    'answer',
    'obtainedGrade',
    'doneDate',
    'taskId',
    'studentId'
  ];

  /**
   * @var int
   */
  public const ITEMS_PER_PAGE = 10;

  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;

  /**
   * @var RequestCheckerService
   */
  private RequestCheckerService $requestCheckerService;

  /**
   * @var ObjectHandlerService
   */
  private ObjectHandlerService $objectHandlerService;

  /**
   * @var TaskService
   */
  private TaskService $taskService;

  /**
   * @var StudentService
   */
  private StudentService $studentService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param RequestCheckerService $requestCheckerService
   * @param TaskService $taskService
   * @param StudentService $studentService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService  $objectHandlerService,
    TaskService $taskService,
    StudentService $studentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->taskService = $taskService;
    $this->studentService = $studentService;
  }

  /**
   * getSubmissions
   *
   * @return mixed
   */
  public function getSubmissions(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $submissions = $this->entityManager->getRepository(Submission::class)->getAllByFilter($requestData, $itemsPerPage, $page);

    return $submissions;
  }

  /**
   * getSubmission
   *
   * @param  string $id
   * @return Submission
   * @throws NotFoundHttpException
   */
  public function getSubmission(string $id): Submission
  {
    $submission = $this->entityManager->getRepository(Submission::class)->findOneBy(['id' => intval($id)]);

    if (!$submission) {
      throw new NotFoundHttpException('Not found submission with id - ' . $id);
    }

    return $submission;
  }

  /**
   * createSubmission
   *
   * @param  mixed $data
   * @return Submission
   */
  public function createSubmission(array $data): Submission
  {
    $this->requestCheckerService::check($data, self::REQUIRED_SUBMISSION_FIELDS);
    $submission = new Submission();

    $task = $this->taskService->getTask($data['taskId']);
    $data['task'] = $task;

    $student = $this->studentService->getStudent($data['studentId']);
    $data['student'] = $student;

    $submission = $this->objectHandlerService->setObjectData($submission, $data);
    $this->entityManager->persist($submission);

    return $submission;
  }

  /**
   * updateSubmission
   *
   * @param  string $id
   * @param  mixed $data
   * @return Submission
   */
  public function updateSubmission(string $id, array $data): Submission
  {
    $submission = $this->getSubmission($id);

    if (array_key_exists('taskId', $data)) {
      $task = $this->taskService->getTask($data['taskId']);
      $data['task'] = $task;
    }

    if (array_key_exists('studentId', $data)) {
      $student = $this->studentService->getStudent($data['studentId']);
      $data['student'] = $student;
    }

    $submission = $this->objectHandlerService->setObjectData($submission, $data);

    return $submission;
  }

  /**
   * deleteSubmission
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteSubmission(string $id): void
  {
    $submission = $this->getSubmission($id);

    $this->entityManager->remove($submission);
  }
}
