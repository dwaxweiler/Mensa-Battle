<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity
 */
class Menu
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="priceStudent", type="float")
     */
    private $priceStudent;

    /**
     * @var float
     *
     * @ORM\Column(name="priceStaff", type="float")
     */
    private $priceStaff;

    /**
     * @var float
     *
     * @ORM\Column(name="priceVisitor", type="float")
     */
    private $priceVisitor;

    /**
     * @var unknown;
     * 
     * @ORM\ManyToMany(targetEntity="DailyMenu", mappedBy="menus")
     */
    private $dailyMenus;
    
    /**
     * @var Meal
     *
     * @ORM\ManyToOne(targetEntity="Meal")
     * @ORM\JoinColumn(name="meal_id", referencedColumnName="id")
     */
    private $meal;
    
    public function __construct()
    {
        $this->dailyMenus = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Menu
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
     * Set priceStudent.
     *
     * @param float $priceStudent
     * @return Menu
     */
    public function setPriceStudent($priceStudent)
    {
        $this->priceStudent = $priceStudent;
      
        return $this;
    }
    
    /**
     * Get priceStudent.
     *
     * @return float
     */
    public function getPriceStudent()
    {
        return $this->priceStudent;
    }
    
    /**
     * Set priceStaff.
     *
     * @param float $priceStaff
     * @return Menu
     */
    public function setPriceStaff($priceStaff)
    {
        $this->priceStaff = $priceStaff;
      
        return $this;
    }
    
    /**
     * Get priceStaff.
     *
     * @return float
     */
    public function getPriceStaff()
    {
        return $this->priceStaff;
    }
    
    /**
     * Set priceVistor.
     *
     * @param float $priceVistor
     * @return Menu
     */
    public function setPriceVistor($priceVistor)
    {
        $this->priceVisitor = $priceVistor;
      
        return $this;
    }
    
    /**
     * Get priceVistor.
     *
     * @return float
     */
    public function getPriceVistor()
    {
        return $this->priceVisitor;
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
     * Set meal.
     *
     * @param Meal $meal
     * @return Menu
     */
    public function setMeal(Meal $meal = null)
    {
        $this->meal = $meal;
    
        return $this;
    }
    
    /**
     * Get meal.
     *
     * @return Meal 
     */
    public function getMeal()
    {
        return $this->meal;
    }
    
    public function toArray()
    {
        $output = $this->meal->toArray();
        $output['name'] = $this->name;
        $output['priceStudent'] = $this->priceStudent;
        $output['priceStaff'] = $this->priceStaff;
        $output['priceVisitor'] = $this->priceVisitor;
        return $output;
    }
}
