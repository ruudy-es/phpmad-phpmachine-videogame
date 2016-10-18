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
        $armorsmith->setName('Armorsmith');
        $armorsmith->setCost(110);

        $manager->persist($armorsmith);

        $this->addReference('armorsmith', $armorsmith);

        $weaponsmith = new TradeSkill();
        $weaponsmith->setName('Weaponsmith');
        $weaponsmith->setCost(80);

        $manager->persist($weaponsmith);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
