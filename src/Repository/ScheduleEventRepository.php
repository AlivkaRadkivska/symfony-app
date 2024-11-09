<?php

namespace App\Repository;

use App\Entity\ScheduleEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduleEvent>
 */
class ScheduleEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleEvent::class);
    }

    /**
     * getAllByFilter
     *
     * @param  array $data
     * @param  int $itemsPerPage
     * @param  int $page
     * @return array
     */
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('scheduleEvent');

        if (isset($data['startDate'])) {
            $queryBuilder->andWhere('scheduleEvent.startDate LIKE :startDate')
                ->setParameter('startDate', '%' . $data['startDate'] . '%');
        }

        if (isset($data['endDate'])) {
            $queryBuilder->andWhere('scheduleEvent.endDate LIKE :endDate')
                ->setParameter('endDate', '%' . $data['endDate'] . '%');
        }

        if (isset($data['courseId'])) {
            $queryBuilder->andWhere('scheduleEvent.course = :course')
                ->setParameter('course', $data['courseId']);
        }

        if (isset($data['groupId'])) {
            $queryBuilder->andWhere('scheduleEvent.group = :group')
                ->setParameter('group', $data['groupId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'scheduleEvents' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
