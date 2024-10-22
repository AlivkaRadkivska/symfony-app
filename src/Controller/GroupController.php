<?php

namespace App\Controller;

use App\Services\Group\GroupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\RequestCheckerService;

#[Route('/group', name: 'group_routes')]
class GroupController extends AbstractController
{
    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestChecker;

    /**
     * @var GroupService
     */
    private GroupService $groupService;

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
     * __construct
     *
     * @param  RequestCheckerService $requestChecker
     * @param  GroupService $groupService
     * @return void
     */
    public function __construct(
        RequestCheckerService $requestChecker,
        GroupService $groupService
    ) {
        $this->requestChecker = $requestChecker;
        $this->groupService = $groupService;
    }

    /**
     * getGroups
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_groups')]
    public function getGroups(): JsonResponse
    {
        $groups = $this->groupService->getGroups();
        return new JsonResponse($groups, Response::HTTP_OK);
    }

    /**
     * addGroup
     *
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/add', name: 'add_group', methods: ['POST'])]
    public function addGroup(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestChecker::check($requestData, self::REQUIRED_GROUP_FIELDS);

        $group = $this->groupService->createGroup($requestData);

        return new JsonResponse($group, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_group')]
    /**
     * getGroup
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function getGroup(string $id): JsonResponse
    {
        $groups = $this->groupService->getGroup($id);
        return new JsonResponse($groups, Response::HTTP_OK);
    }

    /**
     * deleteGroup
     *
     * @param  string $id
     * @return JsonResponse
     */
    #[Route('/delete/{id}', name: 'remove_group', methods: ['DELETE'])]
    public function deleteGroup(string $id): JsonResponse
    {
        $this->groupService->deleteGroup($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * updateGroup
     *
     * @param  string $id
     * @param  Request $request
     * @return JsonResponse
     */
    #[Route('/update/{id}', name: 'update_group', methods: ['PATCH'])]
    public function updateGroup(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $group = $this->groupService->updateGroup($id, $requestData);

        return new JsonResponse($group, Response::HTTP_OK);
    }
}
