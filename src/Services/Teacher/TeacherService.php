<?php

namespace App\Services\Teacher;

use App\Entity\Teacher;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\Department\DepartmentService;

class TeacherService
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
   * @var DepartmentService
   */
  private DepartmentService $departmentService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    DepartmentService $departmentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->departmentService = $departmentService;
  }


  /**
   * getTeachers
   *
   * @return mixed
   */
  public function getTeachers(): mixed
  {
    $teachers = $this->entityManager->getRepository(Teacher::class)->findAll();

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
    $teacher = new Teacher();

    $department = $this->departmentService->getDepartment($data['departmentId']);
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    $teacher
      ->setEmail($data['email'])
      ->setFirstName($data['firstName'])
      ->setLastName($data['lastName'])
      ->setPosition($data['position'])
      ->setDepartment($department)
      ->setPassword($hashedPassword);

    $this->requestCheckerService->validateRequestDataByConstraints($teacher);

    $this->entityManager->persist($teacher);
    $this->entityManager->flush();

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

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'departmentId') {
        $value = $this->departmentService->getDepartment($value);
        $method = 'setDepartment';
      }

      if (!method_exists($teacher, $method)) {
        continue;
      }

      $teacher->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($teacher);
    $this->entityManager->flush();

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
    $this->entityManager->flush();
  }
}
