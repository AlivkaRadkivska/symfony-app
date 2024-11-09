<?php

namespace App\Repository;

use App\Entity\Exam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exam>
 */
class ExamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exam::class);
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
        $queryBuilder = $this->createQueryBuilder('exam');

        if (isset($data['title'])) {
            $queryBuilder->andWhere('exam.title LIKE :title')
                ->setParameter('title', '%' . $data['title'] . '%');
        }

        if (isset($data['description'])) {
            $queryBuilder->andWhere('exam.description LIKE :description')
                ->setParameter('description', '%' . $data['description'] . '%');
        }

        if (isset($data['type'])) {
            $queryBuilder->andWhere('exam.type = :type')
                ->setParameter('type', $data['type']);
        }

        if (isset($data['courseId'])) {
            $queryBuilder->andWhere('exam.course = :course')
                ->setParameter('course', $data['courseId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'exams' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
