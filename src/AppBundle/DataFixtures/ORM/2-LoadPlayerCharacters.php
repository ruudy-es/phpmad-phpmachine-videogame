<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 00:11
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\MapZone;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\PlayerCharacter;

class LoadPlayerCharacters extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var MapZone $winterfell */
        $winterfell = $manager->find('AppBundle:MapZone', 1);
        /** @var MapZone $kingLanding */
        $kingLanding = $manager->find('AppBundle:MapZone', 2);

        $me = new PlayerCharacter();
        $me->setId(1);
        $metadata = $manager->getClassMetadata(get_class($me));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $me->setMapZone($winterfell);
        $me->setName('Me');
        $me->setGold(100);
        $me->setHealth(100);
        $me->setAttack(30);
        $me->setDefense(20);

        $enemy = new PlayerCharacter();
        $enemy->setId(2);
        $metadata = $manager->getClassMetadata(get_class($enemy));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $enemy->setMapZone($kingLanding);
        $enemy->setName('Enemy');
        $enemy->setGold(0);
        $enemy->setHealth(100);
        $enemy->setAttack(40);
        $enemy->setDefense(30);

        $manager->persist($me);
        $manager->persist($enemy);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
