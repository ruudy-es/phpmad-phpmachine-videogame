<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:31
 */

namespace AppBundle\Validator\SwordCrafting;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class IsRecipeLearnedValidator extends ConstraintValidator
{
    /** @var Session */
    protected $session;

    /**
     * IsRecipeLearnedValidator constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $roll = rand(0, 100);
        $this->session->getFlashBag()->add(
            'notice',
            'Rolled a '.$roll.' studying the recipe (70 required).'
        );

        if ($roll < 70) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
