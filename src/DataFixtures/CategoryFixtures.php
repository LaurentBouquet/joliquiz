<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFixtures extends Fixture
{
    public const SYMFONY_REFERENCE = 'Symfony';
    public const SYMFONY_V3_REFERENCE = 'Symfony 3';
    public const SYMFONY_V4_REFERENCE = 'Symfony 4';

    public function load(ObjectManager $manager)
    {
        // SYMFONY_REFERENCE
        $category = new Category();
        $category->setShortname('Symfony');
        $category->setLongname('Symfony (all versions)');
        $manager->persist($category);
        $this->addReference(self::SYMFONY_REFERENCE, $category);

        // SYMFONY_V3_REFERENCE
        $category = new Category();
        $category->setShortname('Symfony3');
        $category->setLongname('Symfony version 3');
        $manager->persist($category);
        $this->addReference(self::SYMFONY_V3_REFERENCE, $category);

        // SYMFONY_V4_REFERENCE
        $category = new Category();
        $category->setShortname('Symfony4');
        $category->setLongname('Symfony version 4');
        $manager->persist($category);
        $this->addReference(self::SYMFONY_V4_REFERENCE, $category);

        $manager->flush();
    }
}
