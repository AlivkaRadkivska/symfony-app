<?php

namespace App\Services\Student;

use App\Entity\Student;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\Group\GroupService;

class StudentService
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
   * @var GroupService
   */
  private GroupService $groupService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param GroupService $groupService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    GroupService $groupService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->groupService = $groupService;
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
    $student = new Student();

    $group = $this->groupService->getGroup($data['groupId']);
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    $student
      ->setEmail($data['email'])
      ->setFirstName($data['firstName'])
      ->setLastName($data['lastName'])
      ->setGroup($group)
      ->setPassword($hashedPassword);

    $this->requestCheckerService->validateRequestDataByConstraints($student);

    $this->entityManager->persist($student);
    $this->entityManager->flush();

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

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'GroupId') {
        $value = $this->groupService->getGroup($value);
        $method = 'setGroup';
      }

      if (!method_exists($student, $method)) {
        continue;
      }

      $student->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($student);
    $this->entityManager->flush();

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
    $this->entityManager->flush();
  }

  // TODO updateCourseList()
}
