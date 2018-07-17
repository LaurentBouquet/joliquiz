<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $em;
    private $param;
    private $language;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $em, ParameterBagInterface $param)
    {
        parent::__construct($registry, Category::class);
        $this->em = $em;
        $this->param = $param;
        $this->language = $this->em->getReference(Language::class, $this->param->get('locale'));
    }

    public function create(): Category
    {
        $category = new Category();
        $category->setLanguage($this->language);
        return $category;
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('c');
        $builder->andWhere('c.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('c.shortname', 'ASC');
        return $builder->getQuery()->getResult();
    }

    /**
     * @return Category[] Returns an array of Category objects
     */
    public function findOneByShortnameAndLanguage($shortname, $language)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.shortname = :shortname')
            ->setParameter('shortname', $shortname)
            ->andWhere('c.language = :language')
            ->setParameter('language', $language)
            ->orderBy('c.shortname', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
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
