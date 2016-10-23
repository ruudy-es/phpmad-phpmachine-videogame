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

//        var_dump($playerCharacter->getTradeSkills());
//        die();

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
            $this->get('session')->getFlashBag()->add('notice', 'The Player already have that Trade Skill');

            return $this->redirectToRoute('homepage');
        }

        // Check if Player Character has enought gold to buy it
        if ($tradeSkill->getCost() > $playerCharacter->getGold()) {
            $this->get('session')->getFlashBag()->add('notice', 'The Player do not have enought gold to buy that');

            return $this->redirectToRoute('homepage');
        }

        $playerCharacter->addTradeSkill($tradeSkill);
        $playerCharacter->setGold(
            $playerCharacter->getGold() - $tradeSkill->getCost()
        );

        $em->persist($playerCharacter);

        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'The Player have learn: ' . $tradeSkill->getName());

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

        // Check the Player Character have it already
        if ($playerCharacterRepository->hasMaterial(
            $playerCharacter->getId(),
            $material->getId()
        )
        ) {
            $this->get('session')->getFlashBag()->add('notice', 'The Player already have that Material');

            return $this->redirectToRoute('homepage');
        }

        // Check if Player Character has enought gold to buy it
        if ($material->getCost() > $playerCharacter->getGold()) {
            $this->get('session')->getFlashBag()->add('notice', 'The Player do not have enought gold to buy that');

            return $this->redirectToRoute('homepage');
        }

        $playerCharacter->addMaterial($material);
        $playerCharacter->setGold(
            $playerCharacter->getGold() - $material->getCost()
        );

        $em->persist($playerCharacter);

        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'The Player have bough: ' . $material->getName());

        return $this->redirectToRoute('homepage');
    }

    public function pvpAction(Request $request)
    {

    }
}
