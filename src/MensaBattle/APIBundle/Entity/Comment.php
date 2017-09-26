<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Comment
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
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;
    
    /**
     * @var Person $author
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
     * Set time when the comment is first persisted.
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
     * Set message.
     *
     * @param string $message
     * @return Comment
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message.
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set author.
     *
     * @param Person $author
     * @return Comment
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
            'message' => $this->message,
            'time' => $this->time->format('Y-m-d H:i:s'));
    }
}