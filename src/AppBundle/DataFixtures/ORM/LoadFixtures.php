<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Genus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $genus = new Genus();
        $genus->setName('Octopus ' . rand(1, 100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 99999));

        $manager->persist($genus);
        $manager->flush();
    }
}