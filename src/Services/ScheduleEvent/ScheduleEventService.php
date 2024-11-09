<?php

namespace App\Services\ScheduleEvent;

use App\Entity\ScheduleEvent;
use App\Services\RequestCheckerService;
use App\Services\ObjectHandlerService;
use App\Services\Course\CourseService;
use App\Services\Group\GroupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ScheduleEventService
{
  /**
   * @var array
   */
  public const REQUIRED_SCHEDULE_EVENT_FIELDS = [
    'meetingLink',
    'startDate',
    'endDate',
    'courseId',
    'groupId'
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
   * @param ObjectHandlerService  $objectHandlerService
   * @param CourseService $courseService
   * @param GroupService $groupService
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    RequestCheckerService  $requestCheckerService,
    ObjectHandlerService  $objectHandlerService,
    CourseService $courseService,
    GroupService $groupService
  ) {
    $this->entityManager = $entityManager;
    $this->requestCheckerService = $requestCheckerService;
    $this->objectHandlerService = $objectHandlerService;
    $this->courseService = $courseService;
    $this->groupService = $groupService;
  }


  /**
   * getScheduleEvents
   *
   * @return mixed
   */
  public function getScheduleEvents(mixed $requestData): mixed
  {
    $itemsPerPage = (int)isset($requestData['itemsPerPage']) ? $requestData['itemsPerPage'] : self::ITEMS_PER_PAGE;
    $page = (int)isset($requestData['page']) ? $requestData['page'] : 1;
    $scheduleEvents = $this->entityManager->getRepository(ScheduleEvent::class)->getAllByFilter($requestData, $itemsPerPage, $page);

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
    $this->requestCheckerService::check($data, self::REQUIRED_SCHEDULE_EVENT_FIELDS);
    $scheduleEvent = new ScheduleEvent();

    $course = $this->courseService->getCourse($data['courseId']);
    $data['course'] = $course;

    $group = $this->groupService->getGroup($data['groupId']);
    $data['group'] = $group;

    $scheduleEvent = $this->objectHandlerService->setObjectData($scheduleEvent, $data);
    $this->entityManager->persist($scheduleEvent);

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

    if (array_key_exists('courseId', $data)) {
      $course = $this->courseService->getCourse($data['courseId']);
      $data['course'] = $course;
    }

    if (array_key_exists('groupId', $data)) {
      $group = $this->groupService->getGroup($data['groupId']);
      $data['group'] = $group;
    }

    $scheduleEvent = $this->objectHandlerService->setObjectData($scheduleEvent, $data);

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
  }
}
