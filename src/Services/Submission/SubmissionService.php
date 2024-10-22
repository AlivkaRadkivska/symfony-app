<?php

namespace App\Services\Submission;

use App\Entity\Submission;
use App\Services\RequestCheckerService;
use App\Services\Task\TaskService;
use App\Services\Student\StudentService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class SubmissionService
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
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    TaskService $taskService,
    StudentService $studentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->taskService = $taskService;
    $this->studentService = $studentService;
  }


  /**
   * getSubmissions
   *
   * @return mixed
   */
  public function getSubmissions(): mixed
  {
    $submissions = $this->entityManager->getRepository(Submission::class)->findAll();

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
    $submission = new Submission();

    $task = $this->taskService->getTask($data['taskId']);
    $student = $this->studentService->getStudent($data['studentId']);

    $submission
      ->setAnswer($data['answer'])
      ->setObtainedGrade($data['obtainedGrade'])
      ->setDoneDate(new DateTime($data['doneDate']))
      ->setTask($task)
      ->setStudent($student);

    $this->requestCheckerService->validateRequestDataByConstraints($submission);

    $this->entityManager->persist($submission);
    $this->entityManager->flush();

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

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'taskId') {
        $value = $this->taskService->getTask($value);
        $method = 'setTask';
      }

      if ($key == 'studentId') {
        $value = $this->studentService->getStudent($value);
        $method = 'setStudent';
      }

      if ($key == 'doneDate') {
        $value = new DateTime($value);
      }

      if (!method_exists($submission, $method)) {
        continue;
      }

      $submission->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($submission);
    $this->entityManager->flush();

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
    $this->entityManager->flush();
  }
}
