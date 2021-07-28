<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    private $tokenStorage;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, User::class);
        $this->tokenStorage = $tokenStorage;
    }

    public function findAll($isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('u');

        if (!$isAdmin) {
            if (!$isTeacher) {
                $builder->andWhere('u.id = :user_id');
                $builder->setParameter('user_id', $this->tokenStorage->getToken()->getUser()->getId());
            }
        }

        $builder->orderBy('u.username', 'ASC');
        return $builder->getQuery()->getResult();
    }

    public function findAllByGroups($groups, $isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('u');

                $builder->innerJoin('u.groups', 'groups');
                $builder->andWhere($builder->expr()->in('groups', ':groups'))->setParameter('groups', $groups);

                // dd($builder->getQuery()->getSQL());


        $builder->orderBy('u.username', 'ASC');
        return $builder->getQuery()->getResult();
    }

    /**
     * @return User Returns an User objects
     */
    public function findOneByEmail($value, $isTeacher = false, $isAdmin = false)
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
