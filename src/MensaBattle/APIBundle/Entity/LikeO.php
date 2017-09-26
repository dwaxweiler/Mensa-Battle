<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LikeO
 *
 * @ORM\Table(name="likeo")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class LikeO
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
     * Get id.
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set time when the like is first persisted.
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
     * @return LikeO
     */
    public function setAuthor(Person $author = null)
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

    public function toArray()
    {
        return array('id' => $this->id,
            'author' => $this->author->getId(),
            'time' => $this->time->format('Y-m-d H:i:s')
            );
    }
}