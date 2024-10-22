<?php

namespace App\Services\Exam;

use App\Entity\Exam;
use App\Services\RequestCheckerService;
use App\Services\Course\CourseService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ExamService
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
   * @var CourseService
   */
  private CourseService $courseService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    CourseService $courseService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->courseService = $courseService;
  }


  /**
   * getExams
   *
   * @return mixed
   */
  public function getExams(): mixed
  {
    $exams = $this->entityManager->getRepository(Exam::class)->findAll();

    return $exams;
  }

  /**
   * getExam
   *
   * @param  string $id
   * @return Exam
   * @throws NotFoundHttpException
   */
  public function getExam(string $id): Exam
  {
    $exam = $this->entityManager->getRepository(Exam::class)->findOneBy(['id' => intval($id)]);

    if (!$exam) {
      throw new NotFoundHttpException('Not found exam with id - ' . $id);
    }

    return $exam;
  }

  /**
   * createExam
   *
   * @param  mixed $data
   * @return Exam
   */
  public function createExam(array $data): Exam
  {
    $exam = new Exam();

    $course = $this->courseService->getCourse($data['courseId']);

    $exam
      ->setTitle($data['title'])
      ->setDescription($data['description'])
      ->setDuration($data['duration'])
      ->setMaxGrade($data['maxGrade'])
      ->setStartDate(new DateTime($data['startDate']))
      ->setCourse($course);

    $this->requestCheckerService->validateRequestDataByConstraints($exam);

    $this->entityManager->persist($exam);
    $this->entityManager->flush();

    return $exam;
  }

  /**
   * updateExam
   *
   * @param  string $id
   * @param  mixed $data
   * @return Exam
   */
  public function updateExam(string $id, array $data): Exam
  {
    $exam = $this->getExam($id);

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'courseId') {
        $value = $this->courseService->getCourse($value);
        $method = 'setCourse';
      }

      if ($key == 'startDate') {
        $value = new DateTime($value);
      }

      if (!method_exists($exam, $method)) {
        continue;
      }

      $exam->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($exam);
    $this->entityManager->flush();

    return $exam;
  }

  /**
   * deleteExam
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteExam(string $id): void
  {
    $exam = $this->getExam($id);

    $this->entityManager->remove($exam);
    $this->entityManager->flush();
  }
}
