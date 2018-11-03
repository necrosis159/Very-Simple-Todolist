<?php

namespace App\Repository;

use App\Entity\TodoTaskList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TodoTaskList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoTaskList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoTaskList[]    findAll()
 * @method TodoTaskList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoTaskListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TodoTaskList::class);
    }

//    /**
//     * @return TodoTaskList[] Returns an array of TodoTaskList objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TodoTaskList
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
