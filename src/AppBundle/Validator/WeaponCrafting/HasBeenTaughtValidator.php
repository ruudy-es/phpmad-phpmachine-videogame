<?php
/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 14:15
 */

namespace AppBundle\Validator\WeaponCrafting;

use AppBundle\Entity\Item;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class HasBeenTaughtValidator extends ConstraintValidator
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
        if (!$this->em->getRepository('PlayerCharacter')
            ->hasRequiredTradeSkill(
                $item->getPlayerCharacter()->getId(),
                $item->getTradeSkill()->getId()
            )
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%tradeskill%', $item->getTradeSkill()->getName())
                ->addViolation();
        }
    }
}
