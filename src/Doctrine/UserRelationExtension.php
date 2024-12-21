<?php

declare(strict_types=1);

namespace App\Extension;

use Doctrine\ORM\QueryBuilder;

abstract class UserRelationExtension extends AbstractCurrentUserExtension
{
  /**
   * @param QueryBuilder $queryBuilder
   * @return void
   */
  public function buildQuery(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ELEMENT_ARRAY];
    $queryBuilder
      ->andWhere($rootAlias . '.user = :user')
      ->setParameter('user', $this->security->getUser());
  }
}
