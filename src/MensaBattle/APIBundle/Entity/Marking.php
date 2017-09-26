<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Marking
 *
 * @ORM\Table(name="marking")
 * @ORM\Entity
 */
class Marking
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
     * @var unknown
     * 
     * @ORM\ManyToMany(targetEntity="Meal", mappedBy="markings")
     */
    private $meals;
    
    public function __construct()
    {
        $this->meals = new ArrayCollection();
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
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Add a meal to this marking.
     * 
     * @param Meal $meal
     * @return Marking
     */
    public function addMeal(Meal $meal)
    {
        $this->meals[] = $meal;
        
        return $this;
    }
    
    /**
     * Remove a meal from this marking.
     * 
     * @param Meal $meal
     */
    public function removeMeal(Meal $meal)
    {
        $this->meals->removeElement($meal);
    }
    
    /**
     * Get meals.
     * 
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMeals()
    {
        return $this->meals;
    }
}
