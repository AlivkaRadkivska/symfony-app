<?php

namespace App\Services\Course;

use App\Entity\Course;
use App\Entity\Student;
use App\Services\RequestCheckerService;
use App\Services\Teacher\TeacherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CourseService
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
   * @var TeacherService
   */
  private TeacherService $teacherService;


  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param TeacherService $teacherService
   * @param StudentService $studentService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    TeacherService $teacherService,
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
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
    $course = new Course();

    $teacher = $this->teacherService->getTeacher($data['teacherId']);

    $course
      ->setName($data['name'])
      ->setDescription($data['description'])
      ->setCredits($data['credits'])
      ->setTeacher($teacher);

    $this->requestCheckerService->validateRequestDataByConstraints($course);

    $this->entityManager->persist($course);
    $this->entityManager->flush();

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

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'TeacherId') {
        $value = $this->teacherService->getTeacher($value);
        $method = 'setTeacher';
      }

      if (!method_exists($course, $method)) {
        continue;
      }

      $course->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($course);
    $this->entityManager->flush();

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
    $this->entityManager->flush();
  }
}
