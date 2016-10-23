<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 00:10
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\PlayerCharacter;
use AppBundle\Entity\TradeSkill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Item;

class LoadItems extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var PlayerCharacter $me */
        $me = $manager->find('AppBundle:PlayerCharacter', 1);
        /** @var TradeSkill $weaponsmith */
        $weaponsmith = $manager->find('AppBundle:TradeSkill', 2);
        /** @var TradeSkill $armorsmith */
        $armorsmith = $manager->find('AppBundle:TradeSkill', 1);

        $sword = new Item();
        $sword->setId(1);
        $metadata = $manager->getClassMetadata(get_class($sword));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $sword->setName('Sword');
        $sword->setTradeSkill($weaponsmith);
        $sword->setPlayerCharacter($me);

        $shield = new Item();
        $shield->setId(2);
        $metadata = $manager->getClassMetadata(get_class($shield));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $shield->setName('Shield');
        $shield->setTradeSkill($armorsmith);
        $shield->setPlayerCharacter($me);

        $ironChest = new Item();
        $ironChest->setId(3);
        $metadata = $manager->getClassMetadata(get_class($ironChest));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $ironChest->setName('Iron Chest');
        $ironChest->setTradeSkill($armorsmith);
        $ironChest->setPlayerCharacter($me);

        $manager->persist($sword);
        $manager->persist($shield);
        $manager->persist($ironChest);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
