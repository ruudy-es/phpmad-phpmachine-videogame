<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 00:10
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Item;

class LoadItems extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sword = new Item();
        $sword->setName('Sword');
        $sword->setTradeSkill($this->getReference('weaponsmith'));
        $sword->setPlayerCharacter($this->getReference('me'));

        $manager->persist($sword);

        $shield = new Item();
        $shield->setName('Shield');
        $shield->setTradeSkill($this->getReference('armorsmith'));
        $shield->setPlayerCharacter($this->getReference('me'));

        $manager->persist($shield);

        $ironChest = new Item();
        $ironChest->setName('Iron Chest');
        $ironChest->setTradeSkill($this->getReference('armorsmith'));
        $ironChest->setPlayerCharacter($this->getReference('me'));

        $manager->persist($ironChest);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
