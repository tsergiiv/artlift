<?php

namespace App\Repository;

use App\Entity\DribbbleShotTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DribbbleShotTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method DribbbleShotTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method DribbbleShotTask[]    findAll()
 * @method DribbbleShotTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DribbbleShotTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DribbbleShotTask::class);
    }

    // /**
    //  * @return DribbbleShotTask[] Returns an array of DribbbleShotTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DribbbleShotTask
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
