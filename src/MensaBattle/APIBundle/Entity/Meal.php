<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Meal
 *
 * @ORM\Table(name="meal")
 * @ORM\Entity
 */
class Meal
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
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    private $description;
    
    /**
     * @var unknown
     * 
     * @ORM\ManyToMany(targetEntity="Marking", inversedBy="meals")
     * @ORM\JoinTable(name="marking_meal")
     */
    private $markings;

    /**
     * @var unknown
     *
     * @ORM\OneToMany(targetEntity="Example", mappedBy="meal")
     */
    private $examples;

    /**
     * @var unknown
     *
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="meal")
     */
    private $ratings;


    public function __construct()
    {
        $this->markings = new ArrayCollection();
        $this->examples = new ArrayCollection();
        $this->ratings = new ArrayCollection();
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
     * @return Meal
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
     * Set description.
     *
     * @param string $description
     * @return Meal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Add marking.
     * 
     * @param Marking $marking
     * @return Meal
     */
    public function addMarking(Marking $marking)
    {
    	  $this->kennzeichnungen[] = $marking;
    
    	  return $this;
    }
    
    /**
     * Remove marking.
     * 
     * @param Marking $marking
     */
    public function removeMarking(Marking $marking)
    {
        $this->markings->removeElement($marking);
    }
    
    /**
     * Delete all markings.
     */
    public function resetMarkings()
    {
        $this->markings = new ArrayCollection();
    }
    
    /**
     * Get markings
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMarkings()
    {
    	  return $this->markings;
    }

    /**
     * Add an example photo for this meal.
     *
     * @param Example $examples
     * @return Meal
     */
    public function addExample(Example $example)
    {
        $this->examples[] = $example;

        return $this;
    }

    /**
     * Remove an example photo from this meal.
     *
     * @param Example $example
     */
    public function removeExample(Example $example)
    {
        $this->examples->removeElement($example);
    }

    /**
     * Get example photos.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getExamples()
    {
        return $this->examples;
    }
    
    /**
     * Get the number of example photos.
     * 
     * @return integer
     */
    public function getNumberExamples()
    {
        return count($this->examples);
    }

    /**
     * Add rating.
     *
     * @param Rating $rating
     * @return Meal
     */
    public function addRating(Rating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating.
     *
     * @param Rating $rating
     */
    public function removeRating(Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        return $this->ratings;
    }
    
    /**
     * Get the number of ratings.
     * 
     * @return integer
     */
    public function getNumberRatings()
    {
        return count($this->ratings);
    }

    /**
     * Create a rating for this meal.
     *
     * @param EntityManager $em entitiy manager
     * @param person $person the person who rates this meal
     * @param score $score person's rating of this meal
     * @return Rating
     */
    public function rate(EntityManager $em, Person $person, $score)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        
        // check if a rating from this user for this meal already exists
        foreach ($this->ratings as $rating)
            if ($rating->getAuthor()->getId() == $person->getId())
                return $rating;
        
        // create rating
        $rating = new Rating();
        $rating->setScore($score)
            ->setAuthor($person)
            ->setMeal($this);
        $em->persist($rating);
        
        // add it to the others
        $this->addRating($rating);
        
        // commit to database
        $em->flush();
        
        return $rating;
    }

    /**
     * Create an example for this meal.
     *
     * @param EntityManager $em entitiy manager
     * @param person $person the person who wants to add an exmaple for this meal
     * @param photo $photo the photo of the person's example
     * @return Example
     */
    public function createExample(EntityManager $em, Person $person, $photo)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        
        // create example
        $example = new Example();
        $example->setMeal($this)
            ->setParticipant($person)
            ->setPhoto($photo);
        $em->persist($example);
        
        // add it to the others
        $this->addExample($example);
        
        // commit to database
        $em->flush();
        
        return $example;
    }

    /**
     * Delete an example from this meal.
     *
     * @param EntityManager $em entity manager
     * @param example $example example to delete
     * @param Person $person person who want's to delete an example
     */
    public function deleteExample(EntityManager $em, Example $example, Person $person)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($example))
            throw new \Exception('Example is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        if ($example->getParticipant() != $person)
            throw new \Exception('You are not the author.');
        
        // remove comment
        $this->removeExample($example);
        $em->remove($example);
        
        // commit to database
        $em->flush();
    }

    /**
     * Calculate and return average rating of this meal.
     *
     * @return average rating of this meal
     */
    public function calculateAverageRating()
    {
        if ($this->ratings->count() == 0)
            return 0;
        
        $avrate = 0;
        foreach ($this->ratings as $rating)
           $avrate += $rating->getScore();
        
        return $avrate / $this->ratings->count();
    }

    /**
     * Create the array for the json response.
     *
     * @return array for the json response
     */
    public function toArray()
    {
        $outputMarkings = array();
        $outputExamples = array();
        $outputRatings = array();
        
        foreach ($this->markings as $marking)
           $outputMarkings[] = $marking->getName();
        
        foreach ($this->examples as $example)
           $outputExamples[] = $example->getId();
  
        foreach ($this->ratings as $rating)
           $outputRatings[] = $rating->getId();
  
        return array(
           'id' => $this->id,
           'title' => $this->title,
           'description' => $this->description,
           'markings' => $outputMarkings,
           'examples' => $outputExamples,
           'ratings' => $outputRatings,
           'averageRating' => $this->calculateAverageRating()
        );
    }
}
