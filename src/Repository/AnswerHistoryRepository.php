<?php

namespace App\Repository;

use App\Entity\AnswerHistory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method AnswerHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnswerHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnswerHistory[]    findAll()
 * @method AnswerHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnswerHistory::class);
    }

//    /**
//     * @return AnswerHistory[] Returns an array of AnswerHistory objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnswerHistory
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
