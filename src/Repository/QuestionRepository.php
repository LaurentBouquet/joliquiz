<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Language;
use App\Entity\Question;
use Pagerfanta\Pagerfanta;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    private $tokenStorage;
    private $language;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, ParameterBagInterface $param, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Question::class);
        $this->em = $em;
        $this->param = $param;
        $this->tokenStorage = $tokenStorage;
        $this->language = $this->em->getReference(Language::class, $this->param->get('locale'));
    }

    public function create(): Question
    {
        $question = new Question();
        $question->setLanguage($this->language);
        return $question;
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Question::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $builder = $this->createQueryBuilder('q');

        $builder->andWhere('q.id = :id');
        $builder->setParameter('id', $id);

        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);

        $builder->orderBy('q.text', 'ASC');
        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findOne($id, $lockMode = null, $lockVersion = null, $isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('q');

        $builder->andWhere('q.id = :id');
        $builder->setParameter('id', $id);

        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);

        if (!$isAdmin) {
            $builder->andWhere('q.created_by = :created_by');
            $builder->setParameter('created_by', $this->tokenStorage->getToken()->getUser());
        }

        $builder->orderBy('q.text', 'ASC');
        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findAll(int $page = 1, $isTeacher = false, $isAdmin = false): Pagerfanta
    {
        $builder = $this->createQueryBuilder('q');

        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);

        if (!$isAdmin) {
            $builder->andWhere('q.created_by = :created_by');
            $builder->setParameter('created_by', $this->tokenStorage->getToken()->getUser());
        }

        $builder->orderBy('q.text', 'ASC');

        //return $builder->getQuery()->getResult();
        return $this->createPaginator($builder->getQuery(), $page);
    }

    public function findAllByCategories($categories, int $page = 1, $isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('q');

        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);

        if (!$isAdmin) {
            $builder->andWhere('q.created_by = :created_by');
            $builder->setParameter('created_by', $this->tokenStorage->getToken()->getUser());
        }

        $builder->innerJoin('q.categories', 'categories');
        $builder->andWhere($builder->expr()->in('categories', ':categories'))->setParameter('categories', $categories);
        // if (!$isTeacher) {
        //     $builder->andWhere('q.active = :active');
        //     $builder->setParameter('active', true);
        // }
        $builder->orderBy('q.text', 'ASC');
        
        if ($page == -1) {            
            return $builder->getQuery()->getResult();
        } else {
            return $this->createPaginator($builder->getQuery(), $page);
        }
    }

    public function findOneRandomByCategories($categories, $isTeacher = false, $isAdmin = false): ?Question
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->innerJoin('q.categories', 'categories');
        $builder->andWhere($builder->expr()->in('categories', ':categories'))->setParameter('categories', $categories);

        $questions = $builder->getQuery()->getResult();
        $question = $questions[rand(1, sizeof($questions)) - 1];

        return $question;
    }

    public function countByCategories($categories, $isTeacher = false, $isAdmin = false): int
    {
        $builder = $this->createQueryBuilder('q');

        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);

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
