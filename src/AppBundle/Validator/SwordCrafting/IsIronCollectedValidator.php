<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:29
 */

namespace AppBundle\Validator\SwordCrafting;

use AppBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class IsIronCollectedValidator extends ConstraintValidator
{
    /** @var Session */
    protected $session;

    /**
     * IsIronCollectedValidator constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param Item $item The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($item, Constraint $constraint)
    {
        $roll = rand(0, 100);
        $this->session->getFlashBag()->add(
            'notice',
            'Rolled a '.$roll.' collecting iron (70 required),'
        );

        if ($roll < 70) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
