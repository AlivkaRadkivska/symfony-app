<?php

namespace App\Services\User;

use App\Entity\User;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\Group\GroupService;
use App\Services\Department\DepartmentService;

class UserService
{
  /**
   * @var array
   */
  public const REQUIRED_USER_FIELDS = [
    'email',
    'password',
    'firstName',
    'lastName',
    'roles'
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
   * @var GroupService
   */
  private GroupService $groupService;

  /**
   * @var DepartmentService
   */
  private DepartmentService $departmentService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService  $objectHandlerService
   * @param GroupService $groupService
   * @param DepartmentService $departmentService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService  $objectHandlerService,
    GroupService $groupService,
    DepartmentService $departmentService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->groupService = $groupService;
    $this->departmentService = $departmentService;
  }


  /**
   * getUsers
   *
   * @return mixed
   */
  public function getUsers(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $users = $this->entityManager->getRepository(user::class)->getAllByFilter($requestData, $itemsPerPage, $page);

    return $users;
  }

  /**
   * getUser
   *
   * @param  string $id
   * @return User
   * @throws NotFoundHttpException
   */
  public function getUser(string $id): User
  {
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => intval($id)]);

    if (!$user) {
      throw new NotFoundHttpException('Not found User with id - ' . $id);
    }

    return $user;
  }

  /**
   * createUser
   *
   * @param  mixed $data
   * @return User
   */
  public function createUser(array $data): User
  {
    $this->requestCheckerService::check($data, self::REQUIRED_USER_FIELDS);
    $user = new User();


    if (array_key_exists('groupId', $data)) {
      $group = $this->groupService->getGroup($data['groupId']);
      $data['group'] = $group;
    }

    if (array_key_exists('departmentId', $data)) {
      $department = $this->departmentService->getDepartment($data['departmentId']);
      $data['department'] = $department;
    }

    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

    $user = $this->objectHandlerService->setObjectData($user, $data);
    $this->entityManager->persist($user);

    return $user;
  }

  /**
   * updateUser
   *
   * @param  string $id
   * @param  mixed $data
   * @return User
   */
  public function updateUser(string $id, array $data): User
  {
    $User = $this->getUser($id);

    if (array_key_exists('groupId', $data)) {
      $group = $this->groupService->getGroup($data['groupId']);
      $data['group'] = $group;
    }

    if (array_key_exists('departmentId', $data)) {
      $department = $this->departmentService->getDepartment($data['departmentId']);
      $data['department'] = $department;
    }

    if (array_key_exists('password', $data)) {
      $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    }

    $User = $this->objectHandlerService->setObjectData($User, $data);

    return $User;
  }

  /**
   * deleteUser
   *
   * @param  string $id
   * @return void
   */
  public function deleteUser(string $id): void
  {
    $User = $this->getUser($id);

    $this->entityManager->remove($User);
  }
}
