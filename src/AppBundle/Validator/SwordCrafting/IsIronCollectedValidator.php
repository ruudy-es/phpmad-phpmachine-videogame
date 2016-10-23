<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:29
 */

namespace AppBundle\Validator\SwordCrafting;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class IsIronCollectedValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param Item $item The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($item, Constraint $constraint)
    {
        $roll = rand(0, 100);

        if ($roll >= 70) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
