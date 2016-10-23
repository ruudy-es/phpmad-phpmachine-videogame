<?php

/**
 * Created by PhpStorm.
 * User: ruudy
 * Date: 19/10/16
 * Time: 08:23
 */

namespace AppBundle\Services;

use AppBundle\Entity\Item;
use AppBundle\Validator\SwordCrafting\HasBeenTaught;
use AppBundle\Validator\SwordCrafting\IsIronCollected;
use AppBundle\Validator\SwordCrafting\IsLeatherBought;
use AppBundle\Validator\SwordCrafting\IsRecipeLearned;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Exception\ExceptionInterface;
use Symfony\Component\Workflow\Workflow as WorkflowComponent;
use Symfony\Component\HttpFoundation\Session\Session;

class SwordCrafting
{
    protected $workflowComponent;
    protected $validator;
    protected $session;
    protected $item;

    /**
     * SwordCrafting constructor.
     *
     * @param WorkflowComponent $workflowComponent
     * @param ValidatorInterface $validator
     */
    public function __construct(
        WorkflowComponent $workflowComponent,
        ValidatorInterface $validator,
        Session $session
    ) {
        $this->workflowComponent = $workflowComponent;
        $this->validator = $validator;
        $this->session = $session;
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

        // Did this on purpose, teaching reasons (Avoid iterations)
        if ($marking->has('draft')) {
            $this->checkHasBeenTaught();
        }

        if ($marking->has('trade_skill_learned')) {
            $this->startRealProcess();
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
        // Did this on purpose, teaching reasons (Avoid events)
        if ($this->workflowComponent->can($this->item, 'knowledge_acquired')) {
            $errors = $this->validateConstraint($this->item, new HasBeenTaught());

            if (count($errors) == 0) {
                $this->workflowComponent->apply($this->item, 'knowledge_acquired');

                $this->session->getFlashBag()->add('notice', 'The Player adquired the needed Trade Skill to buil a Sword');
            } else {
                $this->session->getFlashBag()->add('notice', 'The Player should adquire the needed Trade Skill to buil a Sword');
            }
        }
        // Store messages on flashBag, whatever...
    }

    protected function checkRecipeLearned()
    {
        // Do it like this on purpose, teaching reasons (Avoid events)
        $errors = $this->validateConstraint($this->item, new IsRecipeLearned());

        if (count($errors) == 0) {
            try {
                $this->workflowComponent->apply($this->item, 'reciper_learned');

                $this->session->getFlashBag()->add('notice', 'The Player had learned the recipe to build the Sword.');

                $this->craft();
            } catch (ExceptionInterface $e) {
                $this->session->getFlashBag()->add('danger', $e->getMessage());
            }
        } else {
            $this->session->getFlashBag()->add('notice', 'The Player need to lear the recipe to build the Sword.');
        }
    }

    protected function checkIronIsCollected()
    {
        // Do it like this on purpose, teaching reasons (Avoid events)
        $errors = $this->validateConstraint($this->item, new IsIronCollected());

        if (count($errors) == 0) {
            try {
                $this->workflowComponent->apply($this->item, 'iron_collected');

                $this->session->getFlashBag()->add('notice', 'The Player collected the iron needed for the Sword.');

                $this->craft();
            } catch (ExceptionInterface $e) {
                $this->session->getFlashBag()->add('danger', $e->getMessage());
            }
        } else {
            $this->session->getFlashBag()->add('notice', 'The Player should collect the iron needed for the Sword.');
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

                $this->session->getFlashBag()->add('notice', 'The Player bought the leather needed for the Sword.');

                $this->craft();
            } catch (ExceptionInterface $e) {
                $this->session->getFlashBag()->add('danger', $e->getMessage());
            }
        } else {
            $this->session->getFlashBag()->add('notice', 'The Player need to buy the leather needed for the Sword.');
        }
    }

    protected function startRealProcess()
    {
        try {
            $this->workflowComponent->apply($this->item, 'craft_it');

            $this->session->getFlashBag()->add('notice', 'The Player started crafting the Sword!!!');
        } catch (ExceptionInterface $e) {
            $this->session->getFlashBag()->add('danger', $e->getMessage());
        }
    }

    protected function craft()
    {
        try {
            $this->workflowComponent->apply($this->item, 'craft');

            $this->session->getFlashBag()->add('notice', 'The Player crafted a Sword!!!');
        } catch (ExceptionInterface $e) {
            $this->session->getFlashBag()->add('danger', $e->getMessage());
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

    /**
     * @param ConstraintViolationListInterface $errors
     * @param $type
     */
    protected function fetchErrorsIntoSession(ConstraintViolationListInterface $errors, $type)
    {
        foreach ($errors as $error) {
            $this->session->getFlashBag()->add($type, $error->getMessage());
        }
    }
}
