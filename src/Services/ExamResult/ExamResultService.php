<?php

namespace App\Services\ExamResult;

use App\Entity\ExamResult;
use App\Services\RequestCheckerService;
use App\Services\Exam\ExamService;
use App\Services\Student\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ExamResultService
{
  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;

  /**
   * @var RequestCheckerService
   */
  private RequestCheckerService $requestCheckerService;

  /**
   * @var ExamService
   */
  private ExamService $examService;

  /**
   * @var StudentService
   */
  private StudentService $studentService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ExamService $examService,
    StudentService $studentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->examService = $examService;
    $this->studentService = $studentService;
  }


  /**
   * getExamResults
   *
   * @return mixed
   */
  public function getExamResults(): mixed
  {
    $examResults = $this->entityManager->getRepository(ExamResult::class)->findAll();

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
    $examResult = new ExamResult();

    $exam = $this->examService->getExam($data['examId']);
    $student = $this->studentService->getStudent($data['studentId']);

    $examResult
      ->setAnswer($data['answer'])
      ->setObtainedGrade($data['obtainedGrade'])
      ->setExam($exam)
      ->setStudent($student);

    $this->requestCheckerService->validateRequestDataByConstraints($examResult);

    $this->entityManager->persist($examResult);
    $this->entityManager->flush();

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

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'examId') {
        $value = $this->examService->getExam($value);
        $method = 'setExam';
      }

      if ($key == 'studentId') {
        $value = $this->studentService->getStudent($value);
        $method = 'setStudent';
      }

      if (!method_exists($examResult, $method)) {
        continue;
      }

      $examResult->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($examResult);
    $this->entityManager->flush();

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
    $this->entityManager->flush();
  }
}
