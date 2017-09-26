<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DailyMenu
 *
 * @ORM\Table(name="dailymenu")
 * @ORM\Entity
 */
class DailyMenu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var unknown
     *
     * @ORM\ManyToMany(targetEntity="Menu", inversedBy="dailyMenus")
     * @ORM\JoinTable(name="dailymenu_menu")
     */
    private $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     * @return DailyMenu
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add menus.
     *
     * @param Menu $menus
     * @return DailyMenu
     */
    public function addMenu(Menu $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus.
     *
     * @param Menu $menus
     */
    public function removeMenu(Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Create the array for the json response.
     *
     * @return array for the json response
     */
    public function toArray()
    {
        $outputMenus = array();
  
        foreach ($this->menus as $menu)
           $outputMenus[] = $menu->getId();
  
        return array(
           'id' => $this->id,
           'date' => $this->date->format('Y-m-d'),
           'meals' => $outputMenus
        );
    }
}
