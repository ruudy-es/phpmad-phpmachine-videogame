<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 08:23
 */

namespace AppBundle\Services;

use AppBundle\Entity\Item;
use AppBundle\Validator\WeaponCrafting\HasBeenTaught;
use AppBundle\Validator\WeaponCrafting\IsIronCollected;
use AppBundle\Validator\WeaponCrafting\IsLeatherBought;
use AppBundle\Validator\WeaponCrafting\IsRecipeLearned;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Exception\ExceptionInterface;
use Symfony\Component\Workflow\Workflow as WorkflowComponent;

class WeaponCrafting
{
    protected $workflowComponent;
    protected $validator;
    protected $item;

    /**
     * WeaponCrafting constructor.
     *
     * @param WorkflowComponent $workflowComponent
     * @param ValidatorInterface $validator
     */
    public function __construct(
        WorkflowComponent $workflowComponent,
        ValidatorInterface $validator
    ) {
        $this->workflowComponent = $workflowComponent;
    }

    /**
     * @param Item $item
     */
    public function update(Item $item)
    {
        $this->item = $item;

        $this->transite();
    }

    /**
     * Automatic moves
     */
    protected function transite()
    {
        $marking = $this->workflowComponent->getMarking($this->item);

        // Do it like this on purpose, teaching reasons (Avoid iterations)
        if ($marking->has('draft')) {
            $this->checkHasBeenTaught();
        }

        if ($marking->has('learning_recipe')) {
            $this->checkRecipeLearned();
        }

        if ($marking->has('collecting_iron')) {
            $this->checkIronIsCollected();
        }
    }

    /**
     * Check if required trade skill is learned
     */
    protected function checkHasBeenTaught()
    {
        // Do it like this on purpose, teaching reasons (Avoid events)
        if ($this->workflowComponent->can($this->item, 'knowledge_acquired')) {
            $errors = $this->validateConstraint($this->item, new HasBeenTaught());

            if (count($errors) == 0) {
                $this->workflowComponent->apply($this->item, 'knowledge_acquired');
            }
        }
        // Store messages on flashBag, whatever...
    }

    /**
     * Action launched by the user
     *
     * @param Item $item
     */
    public function startCrafting(Item $item)
    {
        // Manual action
        try {
            $this->workflowComponent->apply($item, 'craft_it');
        } catch (ExceptionInterface $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
    }

    protected function checkRecipeLearned()
    {
        // Do it like this on purpose, teaching reasons (Avoid events)
        $errors = $this->validateConstraint($this->item, new IsRecipeLearned());

        if (count($errors) == 0) {
            try {
                $this->workflowComponent->apply($this->item, 'reciper_learned');

                $this->craft();
            } catch (ExceptionInterface $e) {
                $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
            }
        }
    }

    protected function checkIronIsCollected()
    {
        // Do it like this on purpose, teaching reasons (Avoid events)
        $errors = $this->validateConstraint($this->item, new IsIronCollected());

        if (count($errors) == 0) {
            try {
                $this->workflowComponent->apply($this->item, 'leather_bought');

                $this->craft();
            } catch (ExceptionInterface $e) {
                $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
            }
        }
    }

    /**
     * Action launched by the User
     *
     * @param Item $item
     */
    public function leatherBought(Item $item)
    {
        // Do it like this on purpose, teaching reasons (Avoid events)
        $errors = $this->validateConstraint($item, new IsLeatherBought());

        if (count($errors) == 0) {
            try {
                $this->workflowComponent->apply($item, 'leather_bought');

                $this->craft();
            } catch (ExceptionInterface $e) {
                $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
            }
        }
    }

    protected function craft()
    {
        try {
            $this->workflowComponent->apply($this->item, 'craft');
        } catch (ExceptionInterface $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
    }

    /**
     * @param            $object
     * @param Constraint $constraintClass
     *
     * @return ConstraintViolationListInterface
     */
    private function validateConstraint($object, Constraint $constraintClass)
    {
        return $this->validator->validate(
            $object,
            $constraintClass
        );
    }
}
