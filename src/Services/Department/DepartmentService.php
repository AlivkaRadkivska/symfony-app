<?php

namespace App\Services\Department;

use App\Entity\Department;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class DepartmentService
{
  /**
   * @var array
   */
  public const REQUIRED_DEPARTMENT_FIELDS = [
    'name',
    'faculty'
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
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService $objectHandlerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService $objectHandlerService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
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
    $this->requestCheckerService::check($data, self::REQUIRED_DEPARTMENT_FIELDS);
    $department = new Department();

    $department = $this->objectHandlerService->setObjectData($department, $data);
    $this->entityManager->persist($department);

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

    $department = $this->objectHandlerService->setObjectData($department, $data);

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
  }
}
