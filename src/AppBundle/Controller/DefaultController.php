<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Entity\PlayerCharacter;
use AppBundle\Entity\TradeSkill;
use AppBundle\Services\SwordCrafting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     */
    public function gameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PlayerCharacter $playerCharacter */
        $playerCharacter = $em->getRepository('AppBundle:PlayerCharacter')->findOneBy(['name' => 'Me']);

        $weaponMarking = $em->getRepository('AppBundle:Item')->findOneBy(['name' => 'Sword'])->getMarking();

        // For images only
        ksort($weaponMarking);

        return $this->render('game.html.twig', [
            'player_character' => $playerCharacter,
            'items' => $em->getRepository('AppBundle:Item')->findAll(),
            'weapon_marking' => implode('_', array_keys($weaponMarking)),
            'trade_skills' => $em->getRepository('AppBundle:TradeSkill')->findAll(),
            'materials' => $em->getRepository('AppBundle:Material')->findAll()
        ]);
    }

    /**
     * @Route("/build", name="build")
     * @Method({"POST"})
     */
    public function buildAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Item $item */
        $item = $em->getRepository('AppBundle:Item')->find($request->request->get('item_id'));

        switch ($item->getTradeSkill()->getName()) {
            case 'Weaponsmith':
                /** @var SwordCrafting $weaponCraftingWorkflow */
                $weaponCraftingWorkflow = $this->get('weapon_crafting');
                $weaponCraftingWorkflow->update($item);

                $em->persist($item);
                $em->flush();

                break;
            default:

                break;
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/train", name="train")
     * @Method({"POST"})
     */
    public function trainAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $playerCharacterRepository = $em->getRepository('AppBundle:PlayerCharacter');

        /** @var PlayerCharacter $playerCharacter */
        $playerCharacter = $playerCharacterRepository->findOneBy(['name' => 'Me']);

        /** @var TradeSkill $tradeSkill */
        $tradeSkill = $em->getRepository('AppBundle:TradeSkill')->find($request->request->get('trade_skill_id'));

        // Check the Player Character have it already
        if ($playerCharacterRepository->hasTradeSkill(
                $playerCharacter->getId(),
                $tradeSkill->getId()
            )
        ) {
            $this->get('session')->getFlashBag()->add('notice', $playerCharacter->getName().' already have that Trade Skill');

            return $this->redirectToRoute('homepage');
        }

        // Check if Player Character has enougth gold to buy it
        if ($tradeSkill->getCost() > $playerCharacter->getGold()) {
            $this->get('session')->getFlashBag()->add('notice', $playerCharacter->getName().' do not have enough gold to buy that');

            return $this->redirectToRoute('homepage');
        }

        $this->get('actions')->trainedOn($playerCharacter, $tradeSkill);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/buy", name="buy")
     * @Method({"POST"})
     */
    public function buyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $playerCharacterRepository = $em->getRepository('AppBundle:PlayerCharacter');

        /** @var PlayerCharacter $playerCharacter */
        $playerCharacter = $playerCharacterRepository->findOneBy(['name' => 'Me']);

        /** @var TradeSkill $tradeSkill */
        $material = $em->getRepository('AppBundle:Material')->find($request->request->get('material_id'));

        // Check if the Player Character have it already
        if ($playerCharacterRepository->hasMaterial(
            $playerCharacter->getId(),
            $material->getId()
        )
        ) {
            $this->get('session')->getFlashBag()->add('notice', $playerCharacter->getName().' already have that Material');

            return $this->redirectToRoute('homepage');
        }

        // Check if Player Character has enough gold to buy it
        if ($material->getCost() > $playerCharacter->getGold()) {
            $this->get('session')->getFlashBag()->add('notice', $playerCharacter->getName().' do not have enough gold to buy that');

            return $this->redirectToRoute('homepage');
        }

        $this->get('actions')->bought($playerCharacter, $material);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/player_vs_enemy", name="player_vs_enemy")
     * @Method({"POST"})
     */
    public function pvpAction(Request $request)
    {
        $times = $request->request->get('times');

        $em = $this->getDoctrine()->getManager();

        $playerCharacterRepository = $em->getRepository('AppBundle:PlayerCharacter');

        /** @var PlayerCharacter $playerCharacter */
        $playerCharacter = $playerCharacterRepository->findOneBy(['name' => 'Me']);

        /** @var Item $sword */
        $sword = $em->getRepository('AppBundle:Item')->findOneBy(
            ['name' => 'Sword']
        );

        // Check if the player haves at least one sword
        if ($playerCharacterRepository->hasItem($playerCharacter->getId(), $sword->getId())) {
            // State machine
            $campaignStateMachine = $this->get('player_vs_enemy');
            $campaignStateMachine->update($playerCharacter, $times);

            $em->persist($playerCharacter);
            $em->flush();
        } else {
            $this->get('session')->getFlashBag()->add(
                'notice', 'The game strongly recommends '.$playerCharacter->getName().' to build at least a Sword before go fight with other players'
            );
        }

        return $this->redirectToRoute('homepage');
    }
}
