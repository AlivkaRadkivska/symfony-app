<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserUpdateListener
{
  public function postUpdate(User $entity, PostUpdateEventArgs $args): JsonResponse
  {
    if (!$entity instanceof User) {
      return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $userId = $entity->getId();

    return new JsonResponse("User $userId was updated.", JsonResponse::HTTP_OK);
  }
}
