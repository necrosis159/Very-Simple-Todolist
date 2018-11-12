<?php

namespace App\Repository;

use App\Entity\OutsideAccess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OutsideAccess|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutsideAccess|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutsideAccess[]    findAll()
 * @method OutsideAccess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutsideAccessRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OutsideAccess::class);
    }

//    /**
//     * @return OutsideAccess[] Returns an array of OutsideAccess objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OutsideAccess
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
