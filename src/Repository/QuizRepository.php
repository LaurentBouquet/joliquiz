<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    private $em;
    private $param;
    private $language;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $em, ParameterBagInterface $param)
    {
        parent::__construct($registry, Quiz::class);
        $this->em = $em;
        $this->param = $param;
        $this->language = $this->em->getReference(Language::class, $this->param->get('locale'));
    }

    public function create(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setLanguage($this->language);
        return $quiz;
    }

    public function find($id, $lockMode = NULL, $lockVersion = NULL)
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.id = :id');
        $builder->setParameter('id', $id);
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('q.text', 'ASC');
        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('q.title', 'ASC');
        return $builder->getQuery()->getResult();
    }

//    /**
//     * @return Quiz[] Returns an array of Quiz objects
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
    public function findOneBySomeField($value): ?Quiz
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
