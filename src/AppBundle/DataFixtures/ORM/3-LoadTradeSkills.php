<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 00:07
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\TradeSkill;

class LoadTradeSkills extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $armorsmith = new TradeSkill();
        $armorsmith->setId(1);
        $metadata = $manager->getClassMetadata(get_class($armorsmith));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $armorsmith->setName('Armorsmith');
        $armorsmith->setCost(110);

        $weaponsmith = new TradeSkill();
        $weaponsmith->setId(2);
        $metadata = $manager->getClassMetadata(get_class($weaponsmith));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $weaponsmith->setName('Weaponsmith');
        $weaponsmith->setCost(80);

        $manager->persist($weaponsmith);
        $manager->persist($armorsmith);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
