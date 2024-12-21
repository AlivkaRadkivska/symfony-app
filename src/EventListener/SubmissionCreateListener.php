<?php

namespace App\EventListener;

use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Submission::class)]
class SubmissionCreateListener
{
  public function prePersist(Submission $entity, PrePersistEventArgs $args): void
  {
    $entity->setDoneDate(new \DateTime());
  }
}
