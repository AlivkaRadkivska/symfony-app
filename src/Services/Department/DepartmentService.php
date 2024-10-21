<?php

namespace App\Services\Department;

use App\Entity\Department;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class DepartmentService
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
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
  }


  /**
   * getDepartments
   *
   * @return mixed
   */
  public function getDepartments(): mixed
  {
    $departments = $this->entityManager->getRepository(Department::class)->findAll();

    return $departments;
  }

  /**
   * getDepartment
   *
   * @param  string $id
   * @return Department
   * @throws NotFoundHttpException
   */
  public function getDepartment(string $id): Department
  {
    $department = $this->entityManager->getRepository(Department::class)->findOneBy(['id' => intval($id)]);

    if (!$department) {
      throw new NotFoundHttpException('Not found department with id - ' . $id);
    }

    return $department;
  }

  /**
   * createDepartment
   *
   * @param  mixed $data
   * @return Department
   */
  public function createDepartment(array $data): Department
  {
    $department = new Department();

    $department->setName($data['name'])->setFaculty($data['faculty']);

    $this->requestCheckerService->validateRequestDataByConstraints($department);

    $this->entityManager->persist($department);
    $this->entityManager->flush();

    return $department;
  }

  /**
   * updateDepartment
   *
   * @param  string $id
   * @param  mixed $data
   * @return Department
   */
  public function updateDepartment(string $id, array $data): Department
  {
    $department = $this->getDepartment($id);

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if (!method_exists($department, $method)) {
        continue;
      }

      $department->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($department);
    $this->entityManager->flush();

    return $department;
  }

  /**
   * deleteDepartment
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteDepartment(string $id): void
  {
    $department = $this->getDepartment($id);

    if (!empty($department->getTeachers()) && !empty($department->getGroups())) {
      throw new ConflictHttpException('Department with id - ' . $id . ' cannot be deleted because it is related to teachers or groups.');
    }

    $this->entityManager->remove($department);
    $this->entityManager->flush();
  }
}
