<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:30
 */

namespace AppBundle\Validator\WeaponCrafting;

use Symfony\Component\Validator\Constraint;

class IsLeatherBought extends Constraint
{
    public $message = '';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'weapon_crafting_is_leather_bought_validator';
    }
}
