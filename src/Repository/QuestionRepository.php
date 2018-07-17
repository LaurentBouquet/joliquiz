<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{

    private $em;
    private $param;
    private $language;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $em, ParameterBagInterface $param)
    {
        parent::__construct($registry, Question::class);
        $this->em = $em;
        $this->param = $param;
        $this->language = $this->em->getReference(Language::class, $this->param->get('locale'));
    }

    public function create(): Question
    {
        $question = new Question();
        $question->setLanguage($this->language);
        return $question;
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('q.text', 'ASC');
        return $builder->getQuery()->getResult();
    }

    public function findOneRandomByCategories($categories): ?Question
    {
        $builder = $this->createQueryBuilder('q');
        $builder->innerJoin('q.categories', 'categories');
        $builder->andWhere($builder->expr()->in('categories', ':categories'))->setParameter('categories', $categories);

        $questions = $builder->getQuery()->getResult();
        $question = $questions[rand(1, sizeof($questions))-1];

        return $question;
    }

    public function countByCategories($categories): int
    {
        $builder = $this->createQueryBuilder('q');
        $builder->innerJoin('q.categories', 'categories');
        $builder->andWhere($builder->expr()->in('categories', ':categories'))->setParameter('categories', $categories);

        $questions = $builder->getQuery()->getResult();
        return sizeof($questions);
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
