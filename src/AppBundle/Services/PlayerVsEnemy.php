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
use Symfony\Component\HttpFoundation\Session\Session;

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
    protected $session;

    protected $transitionsDone = 0;
    protected $transitionsLimit;

    public function __construct(
        Factory $SMfactory,
        EntityManager $em,
        Actions $actions,
        Session $session
    ) {
        $this->SMfactory = $SMfactory;
        $this->em = $em;
        $this->actions = $actions;
        $this->session = $session;
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
     * @param $transitionsLimit
     */
    public function update(PlayerCharacter $playerCharacter, $transitionsLimit)
    {
        $this->setStateMachine($playerCharacter);
        $this->transitionsDone = 0;
        $this->transitionsLimit = $transitionsLimit;

        // In charge of recognize automatic state changes on the state machine
        $this->decide();
    }

    public function decide()
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
            $this->decide();
        }
    }

    protected function checkEnemyNear()
    {
        /** @var PlayerCharacter $enemy */
        $enemy = $this->em->getRepository('AppBundle:PlayerCharacter')
            ->findEnemyNear(
                $this->playerCharacter->getId(),
                $this->playerCharacter->getMapZone()
            );

        if (!empty($enemy)) {
            if ($this->stateMachine->can('enemy_near')) {
                $this->stateMachine->apply('enemy_near');

                $this->actions->fightStarts($this->playerCharacter, $enemy);
                $this->actions->fightPhase($this->playerCharacter, $enemy);
                $this->actions->react($enemy);

                $this->transitionsDone++;

                return true;
            }
        } else {
            $this->session->getFlashBag()->add(
                'notice',
                $this->playerCharacter->getName().' did not found enemies on '.$this->playerCharacter->getMapZone()->getName()
            );
            $this->actions->move($this->playerCharacter);
        }

        return false;
    }

    protected function checkEnemyOutOfSign()
    {
        /** @var PlayerCharacter $enemyFightingWith */
        $enemyFightingWith = $this->em->getRepository('AppBundle:PlayerCharacter')->findOneBy(
            ['id' => $this->playerCharacter->getFightingWith()->getId()]
        );

        if (!empty($enemyFightingWith) && $this->playerCharacter->getMapZone() != $enemyFightingWith->getMapZone()
            && $this->stateMachine->can('enemy_out_of_sign')
        ) {
            $this->stateMachine->apply('enemy_out_of_sign');
            $this->session->getFlashBag()->add('notice', $enemyFightingWith->getName().' is not on the same zone.');

            $this->actions->fightEndsFor($this->playerCharacter);

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkEnemyAttackBack()
    {
        /** @var PlayerCharacter $enemyFightingWith */
        $enemyFightingWith = $this->em->getRepository('AppBundle:PlayerCharacter')->findOneBy(
            ['id' => $this->playerCharacter->getFightingWith()->getId()]
        );

        if (!empty($enemyFightingWith) && $enemyFightingWith->getState() == 'attack' &&
            $this->stateMachine->can('enemy_attack_back')
        ) {
            $this->stateMachine->apply('enemy_attack_back');
            $this->session->getFlashBag()->add('notice', $enemyFightingWith->getName().' attacked back.');

            $this->actions->fightPhase($enemyFightingWith, $this->playerCharacter);

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkEnemyIdle()
    {
        /** @var PlayerCharacter $enemyFightingWith */
        $enemyFightingWith = $this->playerCharacter->getFightingWith();

        if (!empty($enemyFightingWith) && $enemyFightingWith && $this->stateMachine->can('enemy_idle')) {
            $this->stateMachine->apply('enemy_idle');
            $this->session->getFlashBag()->add('notice', $this->playerCharacter->getName().' attacks because '.$enemyFightingWith->getName().' is idle.');

            $this->actions->fightPhase($this->playerCharacter, $enemyFightingWith);

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkHealthPointsLow()
    {
        /** @var PlayerCharacter $playerCharacter */
        $playerCharacter = $this->em->getRepository('AppBundle:PlayerCharacter')->findOneBy(['name' => 'Me']);

        $percentage = $playerCharacter->getHealth() * 100 / PlayerCharacter::MAX_HEALTH;

        if ($percentage <= PlayerCharacter::DANGEROUS_PERCENTAGE && $this->stateMachine->can('healthpoints_low')) {
            $this->stateMachine->apply('healthpoints_low');
            $this->session->getFlashBag()->add('notice', $this->playerCharacter->getName().' have to retire to find aid.');

            $this->actions->fightEndsFor($playerCharacter->getFightingWith());

            $this->transitionsDone++;

            return true;
        }

        return false;
    }

    protected function checkAidFound()
    {
        $roll = rand(1, 100);
        $this->session->getFlashBag()->add(
            'notice',
            $this->playerCharacter->getName().' rolled a '.$roll.' finding aid (multiple of 3 required).'
        );

        if ($roll % 3 == 0 && $this->stateMachine->can('aid_found')) {
            $this->stateMachine->apply('aid_found');
            $this->session->getFlashBag()->add(
                'notice',
                $this->playerCharacter->getName().' found some medicinal herbs.'
            );

            $this->actions->heal($this->playerCharacter);
        }

        $this->transitionsDone++;

        return true;
    }
}
