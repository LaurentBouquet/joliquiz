<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('q');
        $builder->orderBy('q.text', 'ASC');
        return $builder->getQuery()->getResult();
    }

    public function findOneByRandomCategories($categories): ?Question
    {
        $builder = $this->createQueryBuilder('q');
        $builder->innerJoin('q.categories', 'categories');
        $builder->andWhere($builder->expr()->in('categories', ':categories'))->setParameter('categories', $categories);

        //TODO ajouter and question not in questionsDejaPosées (liste obtenue par un select dans tbl_history)

        // $builder->setMaxResults(1);
        // $question = $builder->getQuery()->getOneOrNullResult();
        $questions = $builder->getQuery()->getResult();
        $question = $questions[rand(1, sizeof($questions))-1];

        return $question;
    }

//    /**
//     * @return Question[] Returns an array of Question objects
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
    public function findOneBySomeField($value): ?Question
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
