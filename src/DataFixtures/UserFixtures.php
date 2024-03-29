<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public const BASIC_USER_REFERENCE = 'basic_user';
    public const TEACHER_USER_REFERENCE = 'teacher_user';
    public const ADMIN_USER_REFERENCE = 'admin_user';
    public const SUPER_ADMIN_USER_REFERENCE = 'super_admin_user';


    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $superadmin = new User();
        $superadmin->setUsername('superadmin');
        $superadmin->setEmail('superadmin@domain.tld');
        $superadmin->setPlainPassword('superadmin');
        $superadmin->setRoles(array('ROLE_SUPER_ADMIN'));
        $password = $this->userPasswordHasher->encodePassword($superadmin, $superadmin->getPlainPassword());
        $superadmin->setPassword($password);
        $superadmin->setToReceiveMyResultByEmail(false);
        $manager->persist($superadmin);
        $this->addReference(self::SUPER_ADMIN_USER_REFERENCE, $superadmin);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@domain.tld');
        $admin->setPlainPassword('admin');
        $admin->setRoles(array('ROLE_ADMIN'));
        $password = $this->userPasswordHasher->encodePassword($admin, $admin->getPlainPassword());
        $admin->setPassword($password);
        $admin->setToReceiveMyResultByEmail(false);
        $manager->persist($admin);
        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);

        $teacher = new User();
        $teacher->setUsername('teacher');
        $teacher->setEmail('teacher@domain.tld');
        $teacher->setPlainPassword('teacher');
        $teacher->setRoles(array('ROLE_TEACHER'));
        $password = $this->userPasswordHasher->encodePassword($teacher, $teacher->getPlainPassword());
        $teacher->setPassword($password);
        $teacher->setToReceiveMyResultByEmail(false);
        $manager->persist($teacher);
        $this->addReference(self::TEACHER_USER_REFERENCE, $teacher);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@domain.tld');
        $user->setPlainPassword('user');
        $user->setRoles(array('ROLE_USER'));
        $password = $this->userPasswordHasher->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $user->setToReceiveMyResultByEmail(false);
        $manager->persist($user);
        $this->addReference(self::BASIC_USER_REFERENCE, $user);

        $manager->flush();
    }
}
