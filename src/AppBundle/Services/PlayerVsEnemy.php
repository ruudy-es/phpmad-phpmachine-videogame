<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 16:52
 */

namespace AppBundle\Services;

use AppBundle\Entity\PlayerCharacter;
use SM\Factory\Factory;

class PlayerVsEnemy
{
    /** @var Factory $SMfactory */
    protected $SMfactory;

    protected $playerCharacter;
    protected $stateMachine;

    public function __construct(Factory $SMfactory)
    {
        $this->SMfactory = $SMfactory;
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
     *
     * @throws \SM\SMException
     */
    public function update(PlayerCharacter $playerCharacter)
    {
        $this->setStateMachine($playerCharacter);

        // In charge of recognize automatic state changes on the state machine
        $this->transite();
    }

    public function transite()
    {

    }
}
