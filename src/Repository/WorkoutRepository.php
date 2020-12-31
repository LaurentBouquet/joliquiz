<?php

namespace App\Repository;

use App\Entity\Workout;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Workout|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workout|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workout[]    findAll()
 * @method Workout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workout::class);
    }


    public function findLastNotCompletedByStudent($user): ?Workout
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.completed = :completed')
            ->andWhere('w.student = :student')
            ->setParameter('completed', false)
            ->setParameter('student', $user)
            ->orderBy('w.ended_at', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByQuizAndDate($quiz, $date): ?array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.quiz = :quiz')
            ->setParameter('quiz', $quiz)

            ->andWhere('w.started_at >= :started_at')
            ->setParameter('started_at', $date)

            ->groupBy('w.student')
            ->orderBy('w.started_at', 'ASC')
            // ->addOrderBy('w.score', 'DESC')
            // ->addOrderBy('w.ended_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFirstThreeByQuizAndDate($quiz, $date): ?array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.quiz = :quiz')
            ->setParameter('quiz', $quiz)

            ->andWhere('w.started_at >= :started_at')
            ->setParameter('started_at', $date)

            ->groupBy('w.student')
            ->orderBy('w.score', 'DESC')
            ->addOrderBy('w.ended_at', 'DESC')
            ->getQuery()
            ->setMaxResults(3)
            ->getResult()
        ;
    }

//    /**
//     * @return Workout[] Returns an array of Workout objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Workout
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
