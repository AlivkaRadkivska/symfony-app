<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
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
        $queryBuilder = $this->createQueryBuilder('course');

        if (isset($data['name'])) {
            $queryBuilder->andWhere('course.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['description'])) {
            $queryBuilder->andWhere('course.description LIKE :description')
                ->setParameter('description', '%' . $data['description'] . '%');
        }

        if (isset($data['credits'])) {
            $queryBuilder->andWhere('course.credits = :credits')
                ->setParameter('credits', $data['credits']);
        }

        if (isset($data['teacherId'])) {
            $queryBuilder->andWhere('course.teacher = :teacher')
                ->setParameter('teacher', $data['teacherId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'courses' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
