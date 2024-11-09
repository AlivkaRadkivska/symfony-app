<?php

namespace App\Repository;

use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Submission>
 */
class SubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
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
        $queryBuilder = $this->createQueryBuilder('submission');

        if (isset($data['answer'])) {
            $queryBuilder->andWhere('submission.answer LIKE :answer')
                ->setParameter('answer', '%' . $data['answer'] . '%');
        }

        if (isset($data['obtainedGrade'])) {
            $queryBuilder->andWhere('submission.obtainedGrade = :obtainedGrade')
                ->setParameter('obtainedGrade', $data['obtainedGrade']);
        }

        if (isset($data['taskId'])) {
            $queryBuilder->andWhere('submission.task = :task')
                ->setParameter('task', $data['taskId']);
        }

        if (isset($data['studentId'])) {
            $queryBuilder->andWhere('submission.student = :student')
                ->setParameter('student', $data['studentId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'submissions' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
