<?php

namespace App\Services\ScheduleEvent;

use App\Entity\ScheduleEvent;
use App\Services\RequestCheckerService;
use App\Services\Course\CourseService;
use App\Services\Group\GroupService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ScheduleEventService
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
   * @var GroupService
   */
  private GroupService $groupService;

  /**
   * @param EntityManagerInterface $entityManager
   * @param RequestCheckerService $requestCheckerService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    CourseService $courseService,
    GroupService $groupService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->courseService = $courseService;
    $this->groupService = $groupService;
  }


  /**
   * getScheduleEvents
   *
   * @return mixed
   */
  public function getScheduleEvents(): mixed
  {
    $scheduleEvents = $this->entityManager->getRepository(ScheduleEvent::class)->findAll();

    return $scheduleEvents;
  }

  /**
   * getScheduleEvent
   *
   * @param  string $id
   * @return ScheduleEvent
   * @throws NotFoundHttpException
   */
  public function getScheduleEvent(string $id): ScheduleEvent
  {
    $scheduleEvent = $this->entityManager->getRepository(ScheduleEvent::class)->findOneBy(['id' => intval($id)]);

    if (!$scheduleEvent) {
      throw new NotFoundHttpException('Not found ScheduleEvent with id - ' . $id);
    }

    return $scheduleEvent;
  }

  /**
   * createScheduleEvent
   *
   * @param  mixed $data
   * @return ScheduleEvent
   */
  public function createScheduleEvent(array $data): ScheduleEvent
  {
    $scheduleEvent = new ScheduleEvent();

    $course = $this->courseService->getCourse($data['courseId']);
    $group = $this->groupService->getGroup($data['groupId']);

    $scheduleEvent
      ->setStartDate(new DateTime($data['startDate']))
      ->setEndDate(new DateTime($data['endDate']))
      ->setMeetingLink($data['meetingLink'])
      ->setCourse($course)
      ->setGroup($group);

    $this->requestCheckerService->validateRequestDataByConstraints($scheduleEvent);

    $this->entityManager->persist($scheduleEvent);
    $this->entityManager->flush();

    return $scheduleEvent;
  }

  /**
   * updateScheduleEvent
   *
   * @param  string $id
   * @param  mixed $data
   * @return ScheduleEvent
   */
  public function updateScheduleEvent(string $id, array $data): ScheduleEvent
  {
    $scheduleEvent = $this->getScheduleEvent($id);

    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);

      if ($key == 'courseId') {
        $value = $this->courseService->getCourse($value);
        $method = 'setCourse';
      }

      if ($key == 'groupId') {
        $value = $this->groupService->getGroup($value);
        $method = 'setGroup';
      }
      if ($key == 'startDate' || $key == 'endDate') {
        $value = new DateTime($value);
      }

      if (!method_exists($scheduleEvent, $method)) {
        continue;
      }

      $scheduleEvent->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($scheduleEvent);
    $this->entityManager->flush();

    return $scheduleEvent;
  }

  /**
   * deleteScheduleEvent
   *
   * @param  string $id
   * @return void
   * @throws ConflictHttpException
   */
  public function deleteScheduleEvent(string $id): void
  {
    $scheduleEvent = $this->getScheduleEvent($id);

    $this->entityManager->remove($scheduleEvent);
    $this->entityManager->flush();
  }
}
