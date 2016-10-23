<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 23/10/2016
 * Time: 11:12
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Material;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMaterials extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $leather = new Material();
        $leather->setId(1);
        $metadata = $manager->getClassMetadata(get_class($leather));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $leather->setName('Leather');
        $leather->setCost(20);

        $manager->persist($leather);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
