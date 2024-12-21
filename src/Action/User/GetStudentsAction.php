<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Entity\User;
use RuntimeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GetStudentsAction extends AbstractController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  /**
   * __invoke
   *
   * @return array
   */
  public function __invoke(): array
  {
    $students = $this->entityManager
      ->getRepository(User::class)
      ->createQueryBuilder('u')
      ->where('u.roles LIKE :role')
      ->setParameter('role', '%ROLE_STUDENT%')
      ->getQuery()
      ->getResult();

    if (!$students) {
      throw new RuntimeException('Students not found', Response::HTTP_NOT_FOUND);
    }

    return $students;
  }
}
