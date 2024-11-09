<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
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
        $queryBuilder = $this->createQueryBuilder('teacher');

        if (isset($data['name'])) {
            $queryBuilder->andWhere('teacher.firstName LIKE :name OR teacher.lastName LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['email'])) {
            $queryBuilder->andWhere('teacher.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        if (isset($data['position'])) {
            $queryBuilder->andWhere('teacher.position = :position')
                ->setParameter('position', $data['position']);
        }

        if (isset($data['departmentId'])) {
            $queryBuilder->andWhere('department.department = :department')
                ->setParameter('department', $data['departmentId']);
        }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'teachers' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
