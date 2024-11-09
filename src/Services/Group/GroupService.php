<?php

namespace App\Services\Group;

use App\Entity\Group;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\Department\DepartmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class GroupService
{
  /**
   * @var array
   */
  public const REQUIRED_GROUP_FIELDS = [
    'name',
    'major',
    'year',
    'departmentId'
  ];

  /**
   * @var int
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
   * @var ObjectHandlerService
   */
  private ObjectHandlerService $objectHandlerService;

  /**
   * @var DepartmentService
   */
  private DepartmentService $departmentService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService $objectHandlerService
   * @param DepartmentService $departmentService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService $objectHandlerService,
    DepartmentService $departmentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->departmentService = $departmentService;
  }


  /**
   * getGroups
   *
   * @return mixed
   */
  public function getGroups(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $groups = $this->entityManager->getRepository(Group::class)->getAllByFilter($requestData, $itemsPerPage, $page);

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
    $this->requestCheckerService::check($data, self::REQUIRED_GROUP_FIELDS);
    $group = new Group();

    $department = $this->departmentService->getDepartment($data['departmentId']);
    $data['department'] = $department;

    $group = $this->objectHandlerService->setObjectData($group, $data);
    $this->entityManager->persist($group);

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

    if (array_key_exists('departmentId', $data)) {
      $department = $this->departmentService->getDepartment($data['departmentId']);
      $data['department'] = $department;
    }

    $group = $this->objectHandlerService->setObjectData($group, $data);

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
  }
}
