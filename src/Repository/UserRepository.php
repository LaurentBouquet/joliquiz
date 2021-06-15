<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('u');
        $builder->orderBy('u.username', 'ASC');
        return $builder->getQuery()->getResult();
    }

    /**
     * @return User Returns an User objects
     */
    public function findOneByEmail($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }



}
