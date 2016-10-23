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
        $me->setAttack(0);
        $me->setDefense(0);

        $noob = new PlayerCharacter();
        $noob->setId(2);
        $metadata = $manager->getClassMetadata(get_class($noob));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $noob->setMapZone($kingLanding);
        $noob->setName('Noob');
        $noob->setGold(0);
        $noob->setHealth(100);
        $noob->setAttack(30);
        $noob->setDefense(0);

        $pro = new PlayerCharacter();
        $pro->setId(3);
        $metadata = $manager->getClassMetadata(get_class($pro));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $pro->setMapZone($kingLanding);
        $pro->setName('Pro');
        $pro->setGold(0);
        $pro->setHealth(200);
        $pro->setAttack(80);
        $pro->setDefense(29);

        $manager->persist($me);
        $manager->persist($noob);
        $manager->persist($pro);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
