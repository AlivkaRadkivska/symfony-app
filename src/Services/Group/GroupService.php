<?php

namespace App\Services\Group;

use App\Entity\Group;
use App\Services\RequestCheckerService;
use App\Services\Department\DepartmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class GroupService
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
   * getGroups
   *
   * @return mixed
   */
  public function getGroups(): mixed
  {
    $groups = $this->entityManager->getRepository(Group::class)->findAll();

    return $groups;
  }

  /**
   * getGroup
   *
   * @param  string $id
   * @return Group
   * @throws NotFoundHttpException
   */
  public function getGroup(string $id): Group
  {
    $group = $this->entityManager->getRepository(Group::class)->findOneBy(['id' => intval($id)]);

    if (!$group) {
      throw new NotFoundHttpException('Not found group with id - ' . $id);
    }

    return $group;
  }

  /**
   * createGroup
   *
   * @param  mixed $data
   * @return Group
   */
  public function createGroup(array $data): Group
  {
    $group = new Group();

    $department = $this->departmentService->getDepartment($data['departmentId']);

    $group
      ->setName($data['name'])
      ->setMajor($data['major'])
      ->setYear($data['year'])
      ->setDepartment($department);

    $this->requestCheckerService->validateRequestDataByConstraints($group);

    $this->entityManager->persist($group);
    $this->entityManager->flush();

    return $group;
  }

  /**
   * updateGroup
   *
   * @param  string $id
   * @param  mixed $data
   * @return Group
   */
  public function updateGroup(string $id, array $data): Group
  {
    $group = $this->getGroup($id);

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'departmentId') {
        $value = $this->departmentService->getDepartment($value);
        $method = 'setDepartment';
      }

      if (!method_exists($group, $method)) {
        continue;
      }

      $group->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($group);
    $this->entityManager->flush();

    return $group;
  }

  /**
   * deleteGroup
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteGroup(string $id): void
  {
    $group = $this->getGroup($id);

    if (!empty($group->getStudents())) {
      throw new ConflictHttpException('Group with id - ' . $id . ' cannot be deleted because it is related to students.');
    }

    $this->entityManager->remove($group);
    $this->entityManager->flush();
  }
}
