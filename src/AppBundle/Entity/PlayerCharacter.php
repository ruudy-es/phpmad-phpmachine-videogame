<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerCharacter
 *
 * @ORM\Table(name="player_characters")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerCharacterRepository")
 */
class PlayerCharacter
{
    const MAX_HEALTH = 100;
    const DANGEROUS_PERCENTAGE = 40;

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
     * @var int
     *
     * @ORM\Column(name="gold", type="integer")
     */
    private $gold = 100;

    /**
     * @var int
     *
     * @ORM\Column(name="health", type="integer")
     */
    private $health = PlayerCharacter::MAX_HEALTH;

    /**
     * @var int
     *
     * @ORM\Column(name="attack", type="integer")
     */
    private $attack;

    /**
     * @var int
     *
     * @ORM\Column(name="defense", type="integer")
     */
    private $defense;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=45, nullable=true)
     */
    private $state = 'wander';

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="playerCharacter")
     */
    private $items;

    /**
     * @ORM\ManyToMany(targetEntity="TradeSkill")
     * @ORM\JoinTable(name="player_character_trade_skills",
     *      joinColumns={@ORM\JoinColumn(name="player_character_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="trade_skill_id", referencedColumnName="id")}
     *      )
     */
    private $tradeSkills;

    /**
     * @ORM\ManyToMany(targetEntity="Material")
     * @ORM\JoinTable(name="player_character_materials",
     *      joinColumns={@ORM\JoinColumn(name="player_character_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="material_id", referencedColumnName="id")}
     *      )
     */
    private $materials;

    /**
     * @ORM\ManyToOne(targetEntity="MapZone")
     * @ORM\JoinColumn(name="map_zone_id", referencedColumnName="id")
     */
    private $mapZone;

    /**
     * @ORM\OneToOne(targetEntity="PlayerCharacter")
     * @ORM\JoinColumn(name="fighting_with_id", referencedColumnName="id")
     */
    private $fightingWith;

    public function __construct() {
        $this->items = new ArrayCollection();
        $this->tradeSkills = new ArrayCollection();
        $this->materials = new ArrayCollection();
    }

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
     * @return PlayerCharacter
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
     * Set gold
     *
     * @param int $gold
     *
     * @return PlayerCharacter
     */
    public function setGold($gold)
    {
        $this->gold = $gold;

        return $this;
    }

    /**
     * Get gold
     *
     * @return int
     */
    public function getGold()
    {
        return $this->gold;
    }

    /**
     * Set health
     *
     * @param int $health
     *
     * @return PlayerCharacter
     */
    public function setHealth($health)
    {
        $this->health = $health;

        return $this;
    }

    /**
     * Get health
     *
     * @return int
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * Set attack
     *
     * @param int $attack
     *
     * @return PlayerCharacter
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * Get attack
     *
     * @return int
     */
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * Set defense
     *
     * @param int $defense
     *
     * @return PlayerCharacter
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * Get defense
     *
     * @return int
     */
    public function getDefense()
    {
        return $this->defense;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return PlayerCharacter
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add items
     *
     * @param Item $item
     * @return PlayerCharacter
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove items
     *
     * @param Item $item
     */
    public function removeItem(Item $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get tradeskills
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTradeSkills()
    {
        return $this->tradeSkills;
    }

    /**
     * @param TradeSkill $tradeSkill
     *
     * @return PlayerCharacter
     */
    public function addTradeSkill(TradeSkill $tradeSkill)
    {
        $this->tradeSkills[] = $tradeSkill;

        return $this;
    }

    /**
     * @param TradeSkill $tradeSkill
     */
    public function removeTradeSkill(TradeSkill $tradeSkill)
    {
        $this->tradeSkills->removeElement($tradeSkill);
    }

    /**
     * @param Material $material
     *
     * @return PlayerCharacter
     */
    public function addMaterial(Material $material)
    {
        $this->materials[] = $material;

        return $this;
    }

    /**
     * @param Material $material
     */
    public function removeMaterial(Material $material)
    {
        $this->materials->removeElement($material);
    }

    /**
     * Set MapZone
     *
     * @param MapZone $mapZone
     *
     * @return PlayerCharacter
     */
    public function setMapZone(MapZone $mapZone)
    {
        $this->mapZone = $mapZone;

        return $this;
    }

    /**
     * Get MapZone
     *
     * @return MapZone
     */
    public function getMapZone()
    {
        return $this->mapZone;
    }

    /**
     * Set PlayerCharacter
     *
     * @param PlayerCharacter $playerCharacter
     *
     * @return PlayerCharacter
     */
    public function setFightingWith(PlayerCharacter $playerCharacter)
    {
        $this->fightingWith = $playerCharacter;

        return $this;
    }

    /**
     * Get PlayerCharacter
     *
     * @return PlayerCharacter
     */
    public function getFightingWith()
    {
        return $this->fightingWith;
    }
}

