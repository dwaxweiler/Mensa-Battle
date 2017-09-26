<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rating
 *
 * @ORM\Table(name="rating")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Rating
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
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var Meal $meal
     *
     * @ORM\ManyToOne(targetEntity="Meal", inversedBy="ratings")
     * @ORM\JoinColumn(name="meal_id", referencedColumnName="id")
     */
    private $meal;

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
     * Set score.
     *
     * @param integer $score
     * @return Rating
     */
    public function setScore($score)
    {
        $this->score = $score;
  
        return $this;
    }

    /**
     * Get score.
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set time when the rating is first persisted.
     * 
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->time = new \DateTime();
    }

    /**
     * Get time.
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set author.
     *
     * @param Person $author
     * @return Rating
     */
    public function setAuthor(Person $author)
    {
        $this->author = $author;

	      return $this;
    }

    /**
     * Get author.
     *
     * @return Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set meal.
     *
     * @param Meal $meal
     * @return Rating
     */
    public function setMeal(Meal $meal)
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
        return array(
           'id' => $this->id,
           'score' => $this->score,
           'time' => $this->time->format('Y-m-d H:i:s'),
           'author' => $this->author->getId(),
           'meal' => $this->meal->getId()
        );
    }
}
