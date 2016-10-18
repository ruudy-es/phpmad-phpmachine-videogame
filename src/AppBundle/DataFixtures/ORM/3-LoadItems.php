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
