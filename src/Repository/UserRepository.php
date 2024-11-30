<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
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
        $queryBuilder = $this->createQueryBuilder('user');

        if (isset($data['name'])) {
            $queryBuilder->andWhere('user.firstName LIKE :name OR user.lastName LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['email'])) {
            $queryBuilder->andWhere('user.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        // if (isset($data['role'])) {
        //     $queryBuilder->andWhere('user.roles IS :role')
        //         ->setParameter('role', $data['role']);
        // }

        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $paginator
            ->getQuery()->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'users' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
