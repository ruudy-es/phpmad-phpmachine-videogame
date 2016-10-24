<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 23/10/2016
 * Time: 19:42
 */

namespace AppBundle\Services;

use AppBundle\Entity\Material;
use AppBundle\Entity\PlayerCharacter;
use AppBundle\Entity\TradeSkill;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class Actions
{
    protected $em;
    protected $session;

    /**
     * HasBeenTaughtValidator constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * @param PlayerCharacter $player
     */
    public function increaseAttack($player)
    {
        $player->setAttack(
            $player->getAttack() + SwordCrafting::SWORD_ATTACK
        );

        $this->em->persist($player);
        $this->em->flush();

        $this->session->getFlashBag()->add(
            'notice',
            'The Attack of '.$player->getName().' has incresed by '.SwordCrafting::SWORD_ATTACK
        );
    }

    /**
     * @param PlayerCharacter $player
     * @param TradeSkill $tradeSkill
     */
    public function trainedOn($player, $tradeSkill)
    {
        $player->addTradeSkill($tradeSkill);
        $player->setGold(
            $player->getGold() - $tradeSkill->getCost()
        );

        $this->em->persist($player);
        $this->em->flush();

        $this->session->getFlashBag()->add('notice', $player->getName().' have learn: ' . $tradeSkill->getName());
    }

    /**
     * @param PlayerCharacter $player
     * @param Material $material
     */
    public function bought($player, $material)
    {
        $player->addMaterial($material);
        $player->setGold(
            $player->getGold() - $material->getCost()
        );

        $this->em->persist($player);
        $this->em->flush();

        $this->session->getFlashBag()->add('notice', $player->getName().' have bough: ' . $material->getName());
    }

    /**
     * @param PlayerCharacter $player
     */
    public function move($player)
    {
        // Move to another part of the map
        $mapZone = $this->em->getRepository('AppBundle:MapZone')->findAnotherMapZone($player->getMapZone()->getId());

        $player->setMapZone($mapZone);

        $this->em->persist($player);
        $this->em->flush();

        $this->fightEndsFor($player);

        $this->session->getFlashBag()->add('notice', $player->getName().' moved to another zone.');
    }

    /**
     * @param PlayerCharacter $player
     * @param PlayerCharacter $enemy
     */
    public function fightStarts($player, $enemy)
    {
        // We store who we are fighting with
        $player->setFightingWith($enemy);

        $enemy->setFightingWith($player);
        // We force the enemy status to attack back
        $enemy->setState('attack');
        $enemy->setHealth(
            $enemy->getHealth() -$player->getAttack()
        );
        $enemy->setState('attack');

        $this->em->persist($player);
        $this->em->persist($enemy);
        $this->em->flush();

        $this->session->getFlashBag()->add(
            'notice',
            $player->getName().' starts a fight against '.$enemy->getName()
        );
    }

    /**
     * @param PlayerCharacter $enemy
     */
    public function fightEndsFor($enemy)
    {
        $enemy->setFightingWith();
        $enemy->setState('wander');

        $this->em->persist($enemy);
        $this->em->flush();
    }

    /**
     * @param PlayerCharacter $attacker
     * @param PlayerCharacter $defender
     */
    public function fightPhase($attacker, $defender)
    {
        $roll = rand(1, 100);
        $this->session->getFlashBag()->add(
            'notice',
            'Rolled a '.$roll.' on fight phase (50 required).'
        );

        $damage = $attacker->getAttack() - $defender->getDefense();

        if ($roll > 50) {
            $defender->setHealth($defender->getHealth() - $damage);

            $this->session->getFlashBag()->add(
                'notice',
                'During fight phase, '.$defender->getName().' had loose '.($attacker->getAttack() - $defender->getDefense()).' health points'
            );
        } else {
            $this->session->getFlashBag()->add(
                'notice',
                'During fight phase, '.$attacker->getName().' missed the attack'
            );
        }

        $this->em->persist($defender);
        $this->em->flush();
    }

    /**
     * @param PlayerCharacter $player
     */
    public function react($player)
    {
        if ($player->getName() == 'Noob') {
            $this->move($player);
        }
    }

    /**
     * @param PlayerCharacter $player
     */
    public function heal($player)
    {
        $player->setHealth(PlayerCharacter::MAX_HEALTH);

        $this->em->persist($player);
        $this->em->flush();

        $this->session->getFlashBag()->add('notice', $player->getName().' was fully healed.');
    }
}