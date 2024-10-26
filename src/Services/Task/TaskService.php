<?php

namespace App\Services\Task;

use App\Entity\Task;
use App\Services\RequestCheckerService;
use App\Services\Course\CourseService;
use App\Services\ObjectHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskService
{
  /**
   * @var array
   */
  public const REQUIRED_TASK_FIELDS = [
    'title',
    'description',
    'maxGrade',
    'type',
    'dueDate',
    'courseId'
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
   * @var CourseService
   */
  private CourseService $courseService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   * @param ObjectHandlerService  $objectHandlerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService  $objectHandlerService,
    CourseService $courseService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->courseService = $courseService;
  }


  /**
   * getTasks
   *
   * @return mixed
   */
  public function getTasks(): mixed
  {
    $tasks = $this->entityManager->getRepository(Task::class)->findAll();

    return $tasks;
  }

  /**
   * getTask
   *
   * @param  string $id
   * @return Task
   * @throws NotFoundHttpException
   */
  public function getTask(string $id): Task
  {
    $task = $this->entityManager->getRepository(Task::class)->findOneBy(['id' => intval($id)]);

    if (!$task) {
      throw new NotFoundHttpException('Not found task with id - ' . $id);
    }

    return $task;
  }

  /**
   * createTask
   *
   * @param  mixed $data
   * @return Task
   */
  public function createTask(array $data): Task
  {
    $this->requestCheckerService::check($data, self::REQUIRED_TASK_FIELDS);
    $task = new Task();

    $course = $this->courseService->getCourse($data['courseId']);
    $data['course'] = $course;

    $task = $this->objectHandlerService->setObjectData($task, $data);

    $this->entityManager->persist($task);
    $this->entityManager->flush();

    return $task;
  }

  /**
   * updateTask
   *
   * @param  string $id
   * @param  mixed $data
   * @return Task
   */
  public function updateTask(string $id, array $data): Task
  {
    $task = $this->getTask($id);

    if (array_key_exists('courseId', $data)) {
      $course = $this->courseService->getCourse($data['courseId']);
      $data['course'] = $course;
    }

    $task = $this->objectHandlerService->setObjectData($task, $data);
    $this->entityManager->flush();

    return $task;
  }

  /**
   * deleteTask
   *
   * @param  string $id
   * @return void
   */
  public function deleteTask(string $id): void
  {
    $task = $this->getTask($id);

    $this->entityManager->remove($task);
    $this->entityManager->flush();
  }
}
