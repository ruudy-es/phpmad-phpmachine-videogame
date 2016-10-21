<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/game", name="game")
     */
    public function gameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $playerCharacter = $em->getRepository('AppBundle:PlayerCharacter')->findOneBy(['name' => 'Me']);

        $weaponMarking = $em->getRepository('AppBundle:Item')->findOneBy(['name' => 'Sword'])->getMarking();

        return $this->render('game.html.twig', [
            'player_character' => $playerCharacter,
            'weapon_marking' => implode('_', array_keys($weaponMarking))
        ]);
    }
}
