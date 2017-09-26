<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mensa
 *
 * @ORM\Table(name="mensa")
 * @ORM\Entity
 */
class Mensa
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
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var unknown
     *
     * @ORM\ManyToMany(targetEntity="DailyMenu")
     * @ORM\JoinTable(name="mensa_dailymenu",
     *     joinColumns={@ORM\JoinColumn(name="mensa_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="dailymenu_id", referencedColumnName="id", unique=true)}
     *     )
     */
    private $dailyMenus;

    public function __construct()
    {
        $this->dailyMenus = new ArrayCollection();
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
     * Set title.
     *
     * @param string $title
     * @return Mensa
     */
    public function setTitle($title)
    {
        $this->title = $title;
  
        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add daily menu.
     *
     * @param DailyMenu $dailyMenu
     * @return Mensa
     */
    public function addDailyMenu(DailyMenu $dailyMenu)
    {
        $this->dailyMenus[] = $dailyMenu;

        return $this;
    }

    /**
     * Remove daily menu.
     *
     * @param dailyMenu $dailyMenu
     */
    public function removeDailyMenu(DailyMenu $dailyMenu)
    {
        $this->dailyMenus->removeElement($dailyMenu);
    }

    /**
     * Get daily menus.
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDailyMenus()
    {
        return $this->dailyMenus;
    }

    /**
     * Create the array for the json response.
     *
     * @return array for the json response
     */
    public function toArray()
    {
        $outputMenus = array();

        foreach ($this->dailyMenus as $menu)
          $outputMenus[] = $menu->getId();

        return array(
         'id' => $this->id,
         'title' => $this->title,
         'menus' => $outputMenus
        );
    }
}
