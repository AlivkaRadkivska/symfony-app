<?php

declare(strict_types=1);

namespace App\Action\ScheduleEvent;

use App\Entity\ScheduleEvent;
use RuntimeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GetMostRecentScheduleEventAction extends AbstractController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function __invoke(): array
  {
    $events = $this->entityManager
      ->getRepository(ScheduleEvent::class)
      ->createQueryBuilder('se')
      ->where('se.startDate >= :now')
      ->setParameter('now', new \DateTime())
      ->orderBy('se.startDate', 'ASC')
      ->setMaxResults(3)
      ->getQuery()
      ->getResult();

    if (count($events) == 0) {
      throw new RuntimeException('Schedule event not found', Response::HTTP_NOT_FOUND);
    }

    return $events;
  }
}
