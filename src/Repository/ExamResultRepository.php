<?php

namespace App\Repository;

use App\Entity\examResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<examResult>
 */
class ExamResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, examResult::class);
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
        $queryBuilder = $this->createQueryBuilder('examResult');

        if (isset($data['answer'])) {
            $queryBuilder->andWhere('examResult.answer LIKE :answer')
                ->setParameter('answer', '%' . $data['answer'] . '%');
        }

        if (isset($data['obtainedGrade'])) {
            $queryBuilder->andWhere('examResult.obtainedGrade = :obtainedGrade')
                ->setParameter('obtainedGrade', $data['obtainedGrade']);
        }

        if (isset($data['studentId'])) {
            $queryBuilder->andWhere('examResult.student = :student')
                ->setParameter('student', $data['studentId']);
        }

        if (isset($data['examId'])) {
            $queryBuilder->andWhere('examResult.exam = :exam')
                ->setParameter('exam', $data['examId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'examResults' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
