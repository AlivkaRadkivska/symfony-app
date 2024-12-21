<?php

declare(strict_types=1);

namespace App\Action\Course;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeaveCourseAction extends AbstractController
{
  private ManagerRegistry $doctrine;
  private Security $security;

  public function __construct(ManagerRegistry $doctrine, Security $security)
  {
    $this->doctrine = $doctrine;
    $this->security = $security;
  }

  public function __invoke(int $id, Request $request): JsonResponse
  {
    $user = $this->security->getUser();
    if (!$user || !$user instanceof User) {
      return new JsonResponse(['error' => 'Authentication required'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    $entityManager = $this->doctrine->getManager();
    $course = $entityManager->getRepository(Course::class)->find($id);

    if (!$course) {
      return new JsonResponse(['error' => 'Course not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $course->removeStudent($user);
    $entityManager->persist($course);
    $entityManager->flush();

    return new JsonResponse(['success' => 'You have successfully left the course'], JsonResponse::HTTP_OK);
  }
}
