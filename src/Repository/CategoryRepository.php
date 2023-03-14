<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $em;
    private $tokenStorage;
    private $language;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, ParameterBagInterface $param, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Category::class);
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->language = $this->em->getReference(Language::class, $param->get('locale'));
    }

    public function create(): Category
    {
        $category = new Category();
        $category->setLanguage($this->language);
        return $category;
    }

    public function findAll($isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('c');

        $builder->andWhere('c.language = :language');
        $builder->setParameter('language', $this->language);

        if (!$isAdmin) {
            $builder->andWhere('c.created_by = :created_by');
            $builder->setParameter('created_by', $this->tokenStorage->getToken()->getUser());
        }

        $builder->orderBy('c.shortname', 'ASC');
        return $builder->getQuery()->getResult();
    }

    /**
     * @return Category[] Returns an array of Category objects
     */
    public function findOneByShortnameAndLanguage($shortname, $language, $isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('c');

        $builder->andWhere('c.shortname = :shortname');
        $builder->setParameter('shortname', $shortname);

        $builder->andWhere('c.language = :language');
        $builder->setParameter('language', $language);

        if (!$isAdmin) {
            $builder->andWhere('c.created_by = :created_by');
            $builder->setParameter('created_by', $this->tokenStorage->getToken()->getUser());
        }

        $builder->orderBy('c.shortname', 'ASC');

        // $builder->setMaxResults(10);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
