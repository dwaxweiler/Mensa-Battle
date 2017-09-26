<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Example photo
 *
 * @ORM\Table(name="example")
 * @ORM\Entity
 */
class Example extends PhotoUsage
{    
    /**
     * @var unknown
     * 
     * @ORM\ManyToOne(targetEntity="Meal", inversedBy="examplePhotos")
     * @ORM\JoinColumn(name="meal_id", referencedColumnName="id")
     */
    private $meal;

    /**
     * Set meal.
     *
     * @param Meal $meal
     * @return ExamplePhoto
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

    /**
     * (non-PHPdoc)
     * @see PhotoUsage::report()
     */
    public function report(EntityManager $em, Person $person, $message)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        if (!is_string($message))
            throw new \Exception('Message is no string.');
      
        // create report
        $report = new ExampleReport();
        $report->setReporter($person)
            ->setExample($this)
            ->setMessage($message);
        $em->persist($report);
        
        // commit to database
        $em->flush();
        
        return $report;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'meal' => $this->meal->getId(),
            'participant' => $this->participant->getId(),
            'photo' => $this->photo->getId(),
            'beganTime' => $this->beganTime
            );
    }
}