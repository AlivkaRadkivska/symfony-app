<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
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
        $queryBuilder = $this->createQueryBuilder('student');

        if (isset($data['name'])) {
            $queryBuilder->andWhere('student.firstName LIKE :name OR student.lastName LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['email'])) {
            $queryBuilder->andWhere('student.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        if (isset($data['groupId'])) {
            $queryBuilder->andWhere('student.group = :group')
                ->setParameter('group', $data['groupId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'students' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
