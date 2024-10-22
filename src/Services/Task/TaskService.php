<?php

namespace App\Services\Task;

use App\Entity\Task;
use App\Services\RequestCheckerService;
use App\Services\Course\CourseService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class TaskService
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
   * @var CourseService
   */
  private CourseService $courseService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    CourseService $courseService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
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
    $task = new Task();

    $course = $this->courseService->getCourse($data['courseId']);

    $task
      ->setTitle($data['title'])
      ->setDescription($data['description'])
      ->setMaxGrade($data['maxGrade'])
      ->setDueDate(new DateTime($data['dueDate']))
      ->setCourse($course);

    $this->requestCheckerService->validateRequestDataByConstraints($task);

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

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'courseId') {
        $value = $this->courseService->getCourse($value);
        $method = 'setCourse';
      }

      if ($key == 'dueDate') {
        $value = new DateTime($value);
      }

      if (!method_exists($task, $method)) {
        continue;
      }

      $task->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($task);
    $this->entityManager->flush();

    return $task;
  }

  /**
   * deleteTask
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteTask(string $id): void
  {
    $task = $this->getTask($id);

    $this->entityManager->remove($task);
    $this->entityManager->flush();
  }
}
