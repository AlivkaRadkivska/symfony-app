<?php

namespace App\Services\Exam;

use App\Entity\Exam;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\Course\CourseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ExamService
{
  /**
   * @var array
   */
  public const REQUIRED_EXAM_FIELDS = [
    'title',
    'description',
    'maxGrade',
    'duration',
    'type',
    'startDate',
    'courseId'
  ];

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
   * @var CourseService
   */
  private CourseService $courseService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService $objectHandlerService
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService $objectHandlerService,
    CourseService $courseService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
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
    $this->requestCheckerService::check($data, self::REQUIRED_EXAM_FIELDS);
    $exam = new Exam();

    $course = $this->courseService->getCourse($data['courseId']);
    $data['course'] = $course;

    $exam = $this->objectHandlerService->setObjectData($exam, $data);
    $this->entityManager->persist($exam);

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

    if (array_key_exists('courseId', $data)) {
      $course = $this->courseService->getCourse($data['courseId']);
      $data['course'] = $course;
    }

    $exam = $this->objectHandlerService->setObjectData($exam, $data);

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
  }
}
