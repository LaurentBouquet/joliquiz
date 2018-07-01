<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{

    public const BASIC_USER_REFERENCE = 'basic_user';
    public const ADMIN_USER_REFERENCE = 'admin_user';
    public const SUPER_ADMIN_USER_REFERENCE = 'super_admin_user';


    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $superadmin = new User();
        $superadmin->setUsername('superadmin');
        $superadmin->setEmail('superadmin@domain.tld');
        $superadmin->setPlainPassword('superadmin');
        $superadmin->setRoles(array('ROLE_SUPER_ADMIN'));
        $password = $this->passwordEncoder->encodePassword($superadmin, $superadmin->getPlainPassword());
        $superadmin->setPassword($password);
        $manager->persist($superadmin);
        $this->addReference(self::SUPER_ADMIN_USER_REFERENCE, $superadmin);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@domain.tld');
        $admin->setPlainPassword('admin');
        $admin->setRoles(array('ROLE_ADMIN'));
        $password = $this->passwordEncoder->encodePassword($admin, $admin->getPlainPassword());
        $admin->setPassword($password);
        $manager->persist($admin);
        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@domain.tld');
        $user->setPlainPassword('user');
        $user->setRoles(array('ROLE_USER'));
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $manager->persist($user);
        $this->addReference(self::BASIC_USER_REFERENCE, $user);

        $manager->flush();
    }
}
