<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="items")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemRepository")
 */
class Item
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="marking", type="array", length=255, nullable=true)
     */
    private $marking = ['draft' => 1];

    /**
     * @ORM\ManyToOne(targetEntity="PlayerCharacter", inversedBy="items")
     * @ORM\JoinColumn(name="player_character_id", referencedColumnName="id")
     */
    private $playerCharacter;

    /**
     * @ORM\ManyToOne(targetEntity="TradeSkill")
     * @ORM\JoinColumn(name="tradeskill_id", referencedColumnName="id")
     */
    private $tradeskill;

    /**
     * Set id
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set marking
     *
     * @param array $marking
     *
     * @return Item
     */
    public function setMarking($marking)
    {
        $this->marking = $marking;

        return $this;
    }

    /**
     * Get marking
     *
     * @return array
     */
    public function getMarking()
    {
        return $this->marking;
    }

    /**
     * Set PlayerCharacter
     *
     * @param PlayerCharacter $playerCharacter
     *
     * @return Item
     */
    public function setPlayerCharacter(PlayerCharacter $playerCharacter)
    {
        $this->playerCharacter = $playerCharacter;

        return $this;
    }

    /**
     * Get PlayerCharacter
     *
     * @return PlayerCharacter
     */
    public function getPlayerCharacter()
    {
        return $this->playerCharacter;
    }

    /**
     * Set TradeSkill
     *
     * @param TradeSkill $tradeskill
     *
     * @return Item
     */
    public function setTradeSkill(TradeSkill $tradeskill)
    {
        $this->tradeskill = $tradeskill;

        return $this;
    }

    /**
     * Get TradeSkill
     *
     * @return TradeSkill
     */
    public function getTradeSkill()
    {
        return $this->tradeskill;
    }
}

