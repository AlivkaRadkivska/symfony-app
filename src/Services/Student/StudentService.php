<?php

namespace App\Services\Student;

use App\Entity\Student;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\Group\GroupService;
use App\Services\Course\CourseService;

class StudentService
{
  /**
   * @var array
   */
  public const REQUIRED_STUDENT_FIELDS = [
    'email',
    'password',
    'firstName',
    'lastName',
    'groupId'
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
   * @var GroupService
   */
  private GroupService $groupService;

  /**
   * @var CourseService
   */
  private CourseService $courseService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService  $objectHandlerService
   * @param GroupService $groupService
   * @param CourseService $courseService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService  $objectHandlerService,
    GroupService $groupService,
    CourseService $courseService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->groupService = $groupService;
    $this->courseService = $courseService;
  }


  /**
   * getStudents
   *
   * @return mixed
   */
  public function getStudents(): mixed
  {
    $students = $this->entityManager->getRepository(Student::class)->findAll();

    return $students;
  }

  /**
   * getStudent
   *
   * @param  string $id
   * @return Student
   * @throws NotFoundHttpException
   */
  public function getStudent(string $id): Student
  {
    $student = $this->entityManager->getRepository(Student::class)->findOneBy(['id' => intval($id)]);

    if (!$student) {
      throw new NotFoundHttpException('Not found student with id - ' . $id);
    }

    return $student;
  }

  /**
   * createStudent
   *
   * @param  mixed $data
   * @return Student
   */
  public function createStudent(array $data): Student
  {
    $this->requestCheckerService::check($data, self::REQUIRED_STUDENT_FIELDS);
    $student = new Student();

    $group = $this->groupService->getGroup($data['groupId']);
    $data['group'] = $group;

    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

    $student = $this->objectHandlerService->setObjectData($student, $data);
    $this->entityManager->persist($student);

    return $student;
  }

  /**
   * updateStudent
   *
   * @param  string $id
   * @param  mixed $data
   * @return Student
   */
  public function updateStudent(string $id, array $data): Student
  {
    $student = $this->getStudent($id);

    if (array_key_exists('groupId', $data)) {
      $group = $this->groupService->getGroup($data['groupId']);
      $data['group'] = $group;
    }

    if (array_key_exists('password', $data)) {
      $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    }

    $student = $this->objectHandlerService->setObjectData($student, $data);

    return $student;
  }

  /**
   * deleteStudent
   *
   * @param  string $id
   * @return void
   */
  public function deleteStudent(string $id): void
  {
    $student = $this->getStudent($id);

    $this->entityManager->remove($student);
  }

  /**
   * joinCourse
   *
   * @param  string $id
   * @param  string $courseId
   * @return Student
   */
  public function joinCourse(string $id, string $courseId): Student
  {
    $student = $this->getStudent($id);
    $course = $this->courseService->getCourse($courseId);

    $student->addCourse($course);

    return $student;
  }

  /**
   * leaveCourse
   *
   * @param  string $id
   * @param  string $courseId
   * @return Student
   */
  public function leaveCourse(string $id, string $courseId): Student

  {
    $student = $this->getStudent($id);
    $course = $this->courseService->getCourse($courseId);

    $student->removeCourse($course);

    return $student;
  }
}
