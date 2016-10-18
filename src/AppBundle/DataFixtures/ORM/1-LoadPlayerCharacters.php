<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 00:11
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\PlayerCharacter;

class LoadPlayerCharacters extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $me = new PlayerCharacter();
        $me->setName('Me');
        $me->setGold(100);
        $me->setHealth(100);
        $me->setAttack(30);
        $me->setDefense(20);

        $manager->persist($me);

        $this->addReference('me', $me);

        $enemy = new PlayerCharacter();
        $enemy->setName('Enemy');
        $enemy->setGold(0);
        $enemy->setHealth(100);
        $enemy->setAttack(40);
        $enemy->setDefense(30);

        $manager->persist($enemy);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
