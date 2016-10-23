<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:30
 */

namespace AppBundle\Validator\SwordCrafting;

use AppBundle\Entity\Material;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class IsLeatherBoughtValidator extends ConstraintValidator
{
    protected $em;

    /**
     * HasBeenTaughtValidator constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param Item $item The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($item, Constraint $constraint)
    {
        if (!$this->em->getRepository('AppBundle:PlayerCharacter')
            ->hasMaterial(
                $item->getPlayerCharacter()->getId(),
                1 // @TODO table between items and materials (required_materials)
            )
        ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
