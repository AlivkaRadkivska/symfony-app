<?php

namespace App\Services\Course;

use App\Entity\Course;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\Teacher\TeacherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CourseService
{
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
   * @var TeacherService
   */
  private TeacherService $teacherService;


  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService $objectHandlerService
   * @param TeacherService $teacherService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService $objectHandlerService,
    TeacherService $teacherService,
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->teacherService = $teacherService;
  }


  /**
   * getCourses
   *
   * @return mixed
   */
  public function getCourses(): mixed
  {
    $courses = $this->entityManager->getRepository(Course::class)->findAll();

    return $courses;
  }

  /**
   * getCourse
   *
   * @param  string $id
   * @return Course
   * @throws NotFoundHttpException
   */
  public function getCourse(string $id): Course
  {
    $course = $this->entityManager->getRepository(Course::class)->findOneBy(['id' => intval($id)]);

    if (!$course) {
      throw new NotFoundHttpException('Not found course with id - ' . $id);
    }

    return $course;
  }

  /**
   * createCourse
   *
   * @param  mixed $data
   * @return Course
   */
  public function createCourse(array $data): Course
  {
    $this->requestCheckerService::check($data, self::REQUIRED_COURSE_FIELDS);
    $course = new Course();

    $teacher = $this->teacherService->getTeacher($data['teacherId']);
    $data['teacher'] = $teacher;

    $course = $this->objectHandlerService->setObjectData($course, $data);
    $this->entityManager->persist($course);

    return $course;
  }

  /**
   * updateCourse
   *
   * @param  string $id
   * @param  mixed $data
   * @return Course
   */
  public function updateCourse(string $id, array $data): Course
  {
    $course = $this->getCourse($id);

    if (array_key_exists('teacherId', $data)) {
      $teacher = $this->teacherService->getTeacher($data['teacherId']);
      $data['teacher'] = $teacher;
    }

    $course = $this->objectHandlerService->setObjectData($course, $data);

    return $course;
  }

  /**
   * deleteCourse
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteCourse(string $id): void
  {
    $course = $this->getCourse($id);

    $this->entityManager->remove($course);
  }
}
