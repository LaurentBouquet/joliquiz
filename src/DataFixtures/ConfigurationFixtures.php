<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ConfigurationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $configuration1 = new Configuration();
        $configuration1->setConst('MAIN_ALLOW_USER_ACCOUNT_CREATION');
        $configuration1->setDescription('Autoriser la création de nouveaux comptes utilisateurs (register)');
        $configuration1->setValue('1');
        $manager->persist($configuration1);

        $manager->flush();
    }
}
