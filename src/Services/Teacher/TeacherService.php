<?php

namespace App\Services\Teacher;

use App\Entity\Teacher;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\Department\DepartmentService;

class TeacherService
{
  /**
   * @var array
   */
  public const REQUIRED_TEACHER_FIELDS = [
    'email',
    'password',
    'firstName',
    'lastName',
    'position',
    'departmentId'
  ];

  /**
   * @var array
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
   * @var DepartmentService
   */
  private DepartmentService $departmentService;

  /**
   * @var ObjectHandlerService
   */
  private ObjectHandlerService $objectHandlerService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService  $objectHandlerService
   * @param DepartmentService $departmentService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService  $objectHandlerService,
    DepartmentService $departmentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->departmentService = $departmentService;
  }


  /**
   * getTeachers
   *
   * @return mixed
   */
  public function getTeachers(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $teachers = $this->entityManager->getRepository(Teacher::class)->getAllByFilter($requestData, $itemsPerPage, $page);


    return $teachers;
  }

  /**
   * getTeacher
   *
   * @param  string $id
   * @return Teacher
   * @throws NotFoundHttpException
   */
  public function getTeacher(string $id): Teacher
  {
    $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['id' => intval($id)]);

    if (!$teacher) {
      throw new NotFoundHttpException('Not found teacher with id - ' . $id);
    }

    return $teacher;
  }

  /**
   * createTeacher
   *
   * @param  mixed $data
   * @return Teacher
   */
  public function createTeacher(array $data): Teacher
  {
    $this->requestCheckerService::check($data, self::REQUIRED_TEACHER_FIELDS);
    $teacher = new Teacher();

    $department = $this->departmentService->getDepartment($data['departmentId']);
    $data['department'] = $department;

    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

    $teacher = $this->objectHandlerService->setObjectData($teacher, $data);

    $this->entityManager->persist($teacher);

    return $teacher;
  }

  /**
   * updateTeacher
   *
   * @param  string $id
   * @param  mixed $data
   * @return Teacher
   */
  public function updateTeacher(string $id, array $data): Teacher
  {
    $teacher = $this->getTeacher($id);

    if (array_key_exists('departmentId', $data)) {
      $department = $this->departmentService->getDepartment($data['departmentId']);
      $data['department'] = $department;
    }

    if (array_key_exists('password', $data)) {
      $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    }

    $teacher = $this->objectHandlerService->setObjectData($teacher, $data);

    return $teacher;
  }

  /**
   * deleteTeacher
   *
   * @param  string $id
   * @return void
   */
  public function deleteTeacher(string $id): void
  {
    $teacher = $this->getTeacher($id);

    $this->entityManager->remove($teacher);
  }
}
