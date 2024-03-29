<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, User::class);
        $this->tokenStorage = $tokenStorage;
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
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

        $builder->orderBy('u.lastname', 'ASC');
        return $builder->getQuery()->getResult();
    }

    public function findAllByGroups($groups, $isTeacher = false, $isAdmin = false)
    {
        $builder = $this->createQueryBuilder('u');

        if ($groups) {
            $builder->innerJoin('u.groups', 'groups');
            $builder->andWhere($builder->expr()->in('groups', ':groups'))->setParameter('groups', $groups);
        } else {
            if (!$isAdmin) {
                if (!$isTeacher) {
                    $builder->innerJoin('u.groups', 'groups');
                    $builder->andWhere($builder->expr()->in('groups', ':groups'))->setParameter('groups', $groups);
                }
            }
        }        
        // dd($builder->getQuery()->getSQL());

        $builder->orderBy('u.lastQuizAccess', 'ASC');
        $builder->addOrderBy('u.lastname', 'ASC');
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


//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
