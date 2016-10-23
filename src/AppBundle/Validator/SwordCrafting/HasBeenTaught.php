<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:15
 */

namespace AppBundle\Validator\SwordCrafting;

use Symfony\Component\Validator\Constraint;

class HasBeenTaught extends Constraint
{
    public $message = 'The Player do not have the required trade skill yet: %tradeskill%';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'weapon_crafting_has_been_taught_validator';
    }
}
