<?php

namespace App\Repository;

use App\Entity\Shot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shot[]    findAll()
 * @method Shot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shot::class);
    }

    // /**
    //  * @return Shot[] Returns an array of Shot objects
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
    public function findOneBySomeField($value): ?Shot
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
