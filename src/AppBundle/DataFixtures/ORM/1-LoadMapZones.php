<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 22/10/2016
 * Time: 17:00
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\MapZone;

class LoadMapZones extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $winterfell = new MapZone();
        $winterfell->setId(1);
        $metadata = $manager->getClassMetadata(get_class($winterfell));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $winterfell->setName('Winterfell');

        $kings_landing = new MapZone();
        $kings_landing->setId(2);
        $metadata = $manager->getClassMetadata(get_class($kings_landing));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $kings_landing->setName('Kings Landing');

        $manager->persist($winterfell);
        $manager->persist($kings_landing);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}