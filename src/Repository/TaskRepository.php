<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
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
        $queryBuilder = $this->createQueryBuilder('task');

        if (isset($data['title'])) {
            $queryBuilder->andWhere('task.title LIKE :title')
                ->setParameter('title', '%' . $data['title'] . '%');
        }

        if (isset($data['description'])) {
            $queryBuilder->andWhere('task.description LIKE :description')
                ->setParameter('description', '%' . $data['description'] . '%');
        }

        if (isset($data['maxGrade'])) {
            $queryBuilder->andWhere('task.maxGrade = :maxGrade')
                ->setParameter('maxGrade', $data['maxGrade']);
        }

        if (isset($data['courseId'])) {
            $queryBuilder->andWhere('task.course = :course')
                ->setParameter('course', $data['courseId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'tasks' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
