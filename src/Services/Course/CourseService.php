<?php

namespace App\Services\Course;

use App\Entity\Course;
use App\Entity\User;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CourseService
{
  /**
   * @var array
   */
  private const REQUIRED_COURSE_FIELDS = [
    'name',
    'description',
    'credits',
    'teacherId'
  ];

  /**
   * @var int
   */
  private const ITEMS_PER_PAGE = 10;

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
   * @var UserService
   */
  private UserService $userService;


  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService $objectHandlerService
   * @param UserService $userService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService $objectHandlerService,
    UserService $userService,
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->userService = $userService;
  }

  /**
   * getCourses
   *
   * @return mixed
   */
  public function getCourses(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $courses = $this->entityManager->getRepository(Course::class)->getAllByFilter($requestData, $itemsPerPage, $page);

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

    $teacher = $this->userService->getUser($data['teacherId']);
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
      $teacher = $this->userService->getUser($data['teacherId']);
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

  /**
   * joinCourse
   *
   * @param  string $id
   * @param  User $student
   * @return Course
   */
  public function joinCourse(string $id, User $student): Course
  {
    $course = $this->getCourse($id);
    $course->addStudent($student);

    return $course;
  }

  /**
   * leaveCourse
   *
   * @param  string $id
   * @param  User $student
   * @return User
   */
  public function leaveCourse(string $id, User $student): Course
  {
    $course = $this->getCourse($id);
    $course->removeStudent($student);

    return $course;
  }
}
