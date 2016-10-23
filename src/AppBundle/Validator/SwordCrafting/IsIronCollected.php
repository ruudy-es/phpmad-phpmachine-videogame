<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:29
 */

namespace AppBundle\Validator\SwordCrafting;

use Symfony\Component\Validator\Constraint;

class IsIronCollected extends Constraint
{
    public $message = '';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'weapon_crafting_is_iron_collected_validator';
    }
}
