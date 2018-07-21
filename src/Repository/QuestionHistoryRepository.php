<?php

namespace App\Repository;

use App\Entity\QuestionHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method QuestionHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionHistory[]    findAll()
 * @method QuestionHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QuestionHistory::class);
    }


        public function findLastByWorkout($workout): ?QuestionHistory
        {
            return $this->createQueryBuilder('qh')
                ->andWhere('qh.workout = :workout')
                ->setParameter('workout', $workout)
                ->orderBy('qh.started_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

        public function findAllByWorkout($workout)
        {
            return $this->createQueryBuilder('qh')
                ->andWhere('qh.workout = :workout')
                ->setParameter('workout', $workout)
                ->orderBy('qh.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;
        }



//    /**
//     * @return QuestionHistory[] Returns an array of QuestionHistory objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuestionHistory
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
