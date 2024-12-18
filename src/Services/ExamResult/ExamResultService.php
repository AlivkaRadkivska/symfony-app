<?php

namespace App\Services\ExamResult;

use App\Entity\ExamResult;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\Exam\ExamService;
use App\Services\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ExamResultService
{
  /**
   * @var array
   */
  public const REQUIRED_EXAM_RESULT_FIELDS = [
    'answer',
    'obtainedGrade',
    'examId',
    'userId'
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
   * @var ExamService
   */
  private ExamService $examService;

  /**
   * @var UserService
   */
  private UserService $userService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService $objectHandlerService
   * @param ExamService $examService
   * @param UserService $userService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService $objectHandlerService,
    ExamService $examService,
    UserService $userService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->examService = $examService;
    $this->userService = $userService;
  }

  /**
   * getExamResults
   *
   * @return mixed
   */
  public function getExamResults(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $examResults = $this->entityManager->getRepository(ExamResult::class)->getAllByFilter($requestData, $itemsPerPage, $page);


    return $examResults;
  }

  /**
   * getExamResult
   *
   * @param  string $id
   * @return ExamResult
   * @throws NotFoundHttpException
   */
  public function getExamResult(string $id): ExamResult
  {
    $examResult = $this->entityManager->getRepository(ExamResult::class)->findOneBy(['id' => intval($id)]);

    if (!$examResult) {
      throw new NotFoundHttpException('Not found exam result with id - ' . $id);
    }

    return $examResult;
  }

  /**
   * createExamResult
   *
   * @param  mixed $data
   * @return ExamResult
   */
  public function createExamResult(array $data): ExamResult
  {
    $this->requestCheckerService::check($data, self::REQUIRED_EXAM_RESULT_FIELDS);
    $examResult = new ExamResult();

    $exam = $this->examService->getExam($data['examId']);
    $data['exam'] = $exam;

    $student = $this->userService->getUser($data['studentId']);
    $data['student'] = $student;

    $examResult = $this->objectHandlerService->setObjectData($examResult, $data);
    $this->entityManager->persist($examResult);

    return $examResult;
  }

  /**
   * updateExamResult
   *
   * @param  string $id
   * @param  mixed $data
   * @return ExamResult
   */
  public function updateExamResult(string $id, array $data): ExamResult
  {
    $examResult = $this->getExamResult($id);

    if (array_key_exists('examId', $data)) {
      $exam = $this->examService->getExam($data['examId']);
      $data['exam'] = $exam;
    }

    if (array_key_exists('studentId', $data)) {
      $student = $this->userService->getUser($data['studentId']);
      $data['student'] = $student;
    }

    $examResult = $this->objectHandlerService->setObjectData($examResult, $data);

    return $examResult;
  }

  /**
   * deleteExamResult
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteExamResult(string $id): void
  {
    $examResult = $this->getExamResult($id);

    $this->entityManager->remove($examResult);
  }
}
