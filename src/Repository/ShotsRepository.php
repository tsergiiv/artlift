<?php

namespace App\Repository;

use App\Entity\Shots;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shots|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shots|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shots[]    findAll()
 * @method Shots[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShotsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shots::class);
    }

    // /**
    //  * @return Shots[] Returns an array of Shots objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Shots
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
