<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:31
 */

namespace AppBundle\Validator\SwordCrafting;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class IsRecipeLearnedValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
        $roll = rand(0, 100);

        if ($roll >= 70) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
