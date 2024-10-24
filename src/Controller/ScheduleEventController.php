<?php

namespace App\Controller;

use App\Services\ScheduleEvent\ScheduleEventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/scheduleEvent', name: 'schedule_event_routes')]
class ScheduleEventController extends AbstractController
{
    /**
     * @var ScheduleEventService
     */
    private ScheduleEventService $scheduleEventService;

    /**
     * __construct
     *
     * @param  ScheduleEventService $ScheduleEventService
     * @return void
     */
    public function __construct(
        ScheduleEventService $scheduleEventService
    ) {
        $this->scheduleEventService = $scheduleEventService;
    }

    /**
     * getScheduleEvents
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_schedule_event')]
    public function getScheduleEvents(): JsonResponse
    {
        $scheduleEvents = $this->scheduleEventService->getScheduleEvents();
        return new JsonResponse($scheduleEvents, Response::HTTP_OK);
    }

    /**
     * addScheduleEvent
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_schedules_event', methods: ['POST'])]
    public function addScheduleEvent(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $scheduleEvent = $this->scheduleEventService->createScheduleEvent($requestData);
        return new JsonResponse($scheduleEvent, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_schedule_event')]
    /**
     * getScheduleEvent
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function getScheduleEvent(string $id): JsonResponse
    {
        $scheduleEvents = $this->scheduleEventService->getScheduleEvent($id);
        return new JsonResponse($scheduleEvents, Response::HTTP_OK);
    }

    /**
     * deleteScheduleEvent
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_schedule_event', methods: ['DELETE'])]
    public function deleteScheduleEvent(string $id): JsonResponse
    {
        $this->scheduleEventService->deleteScheduleEvent($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateScheduleEvent
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_schedule_event', methods: ['PATCH'])]
    public function updateScheduleEvent(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $scheduleEvent = $this->scheduleEventService->updateScheduleEvent($id, $requestData);
        return new JsonResponse($scheduleEvent, Response::HTTP_OK);
    }
}
