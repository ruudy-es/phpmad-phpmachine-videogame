<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 16:52
 */

namespace AppBundle\Services;

use AppBundle\Entity\PlayerCharacter;
use Doctrine\ORM\EntityManager;
use SM\Factory\Factory;
use SM\StateMachine\StateMachineInterface;

class PlayerVsEnemy
{
    const MAX_HEALTH = 100;

    protected $em;
    /** @var Factory $SMfactory */
    protected $SMfactory;
    /** @var PlayerCharacter */
    protected $playerCharacter;
    /** @var StateMachineInterface */
    protected $stateMachine;

    protected $transitionsDone = 0;
    protected $transitionsLimit;

    public function __construct(Factory $SMfactory, EntityManager $em)
    {
        $this->SMfactory = $SMfactory;
        $this->em = $em;
    }

    protected function setStateMachine(PlayerCharacter $playerCharacter)
    {
        $this->playerCharacter = $playerCharacter;
        $this->stateMachine = $this->SMfactory->get($playerCharacter, 'simple');
    }

    /**
     * AUTOMATIC TRANSACTIONS RECOGNITION.
     */

    /**
     * @param PlayerCharacter $playerCharacter
     * @param $number
     */
    public function update(PlayerCharacter $playerCharacter, $number)
    {
        $this->setStateMachine($playerCharacter);
        $this->transitionsDone = 0;
        $this->transitionsLimit = $number;

        // In charge of recognize automatic state changes on the state machine
        $this->transite();
    }

    public function transite()
    {
        // Did this on purpose, teaching reasons (Avoid iterations)
        switch ($this->stateMachine->getState()) {
            case 'wander':
                if ($this->transitionsDone < $this->transitionsLimit) {
                    $this->checkEnemyNear();
                }

                break;
            case 'attack':
                if ($this->transitionsDone < $this->transitionsLimit) {
                    if ($this->checkEnemyOutOfSign() === false) {
                        $this->checkEnemyAttackBack();
                    }
                }

                break;
            case 'evade':
                if ($this->transitionsDone < $this->transitionsLimit) {
                    if ($this->checkHealthPointsLow() === false) {
                        $this->checkEnemyIdle();
                    }
                }

                break;
            case 'find_aid':
                if ($this->transitionsDone < $this->transitionsLimit) {
                    $this->checkAidFound();
                }

                break;
        }

        if ($this->transitionsDone < $this->transitionsLimit) {
            $this->transite();
        }
    }

    protected function checkEnemyNear()
    {
        $enemy = $this->em->getRepository('PlayerCharacter')->findEnemyNear(
            $this->playerCharacter->getId(),
            $this->playerCharacter->getMapZone()
        );

        if (!empty($enemy)) {
            if ($this->stateMachine->can('enemy_near')) {
                $this->stateMachine->apply('enemy_near');

                // We store who we are fighting with
                $this->playerCharacter->setFightingWith($enemy);
                $enemy->setFightingWith($this->playerCharacter);

                $this->em->persist($this->playerCharacter);
                $this->em->persist($enemy);

                $this->em->flush();

                // Session

                $this->transitionsDone++;

                return true;
            }
        } else {
            // Move to another part of the map
            $mapZone = $this->em->getRepository('MapZone')->findAnotherMapZone($this->playerCharacter->getMapZone()->getId());

            $this->playerCharacter->setMapZone($mapZone);

            $this->em->persist($this->playerCharacter);
        }

        return false;
    }

    protected function checkEnemyOutOfSign()
    {
        /** @var PlayerCharacter $enemyFightingWith */
        $enemyFightingWith = $this->em->getRepository('PlayerCharacter')->findOneBy(
            ['id' => $this->playerCharacter->getFightingWith()->getId()]
        );

        if (!empty($enemyFightingWith) && $this->playerCharacter->getMapZone() != $enemyFightingWith->getMapZone()
            && $this->stateMachine->can('enemy_out_of_sign')
        ) {
            $this->stateMachine->apply('enemy_out_of_sign');

            // Session

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkEnemyAttackBack()
    {
        /** @var PlayerCharacter $enemyFightingWith */
        $enemyFightingWith = $this->em->getRepository('PlayerCharacter')->findOneBy(
            ['id' => $this->playerCharacter->getFightingWith()->getId()]
        );

        if (!empty($enemyFightingWith) && $enemyFightingWith->getState() == 'attack' && $this->stateMachine->can('enemy_attack_back')) {
            $this->stateMachine->apply('enemy_attack_back');

            // Session

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkEnemyIdle()
    {
        $enemy = $this->em->getRepository('PlayerCharacter')->findEnemyOnState($this->playerCharacter->getId(), 'find_aid');

        if (!empty($enemy) && $this->stateMachine->can('enemy_idle')) {
            $this->stateMachine->apply('enemy_idle');

            // Session

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkHealthPointsLow()
    {
        $playerCharacter = $this->em->getRepository('AppBundle:PlayerCharacter')->findOneBy(['name' => 'Me']);

        $percentage = $playerCharacter->getHealth() * 100 / PlayerCharacter::MAX_HEALTH;

        if ($percentage <= PlayerCharacter::DANGEROUS_PERCENTAGE && $this->stateMachine->can('healthpoints_low')) {
            $this->stateMachine->apply('healthpoints_low');

            // Session

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkAidFound()
    {
        $roll = rand(0, 100);

        if ($roll % 3 === 0 && $this->stateMachine->can('aid_found')) {
            $this->stateMachine->apply('aid_found');

            // Session

            $this->transitionsDone++;

            return true;
        }

        return false;
    }
}
