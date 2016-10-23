<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:30
 */

namespace AppBundle\Validator\SwordCrafting;

use Symfony\Component\Validator\Constraint;

class IsLeatherBought extends Constraint
{
    public $message = 'The leather required for the sword is not bought yet';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'weapon_crafting_is_leather_bought_validator';
    }
}
