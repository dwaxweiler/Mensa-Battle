<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Photo usage
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class PhotoUsage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="beganTime", type="datetime")
     */
    protected $beganTime;

    /**
     * @var Person
     * 
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $participant;
    
    /**
     * @var Photo
     * 
     * @ORM\ManyToOne(targetEntity="Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     */
    protected $photo;

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
     * Set time when the photo usage is first persisted.
     * 
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->beganTime = new \DateTime();
    }

    /**
     * Get beganTime.
     *
     * @return \DateTime 
     */
    public function getBeganTime()
    {
        return $this->beganTime;
    }

    /**
     * Set participant.
     *
     * @param Person $participant
     * @return PhotoUsage
     */
    public function setParticipant(Person $participant = null)
    {
        $this->participant = $participant;
    
        return $this;
    }

    /**
     * Get participant.
     *
     * @return Person 
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * Set photo.
     *
     * @param Photo $photo
     * @return PhotoUsage
     */
    public function setPhoto(Photo $photo = null)
    {
        $this->photo = $photo;
    
        return $this;
    }

    /**
     * Get photo.
     *
     * @return Photo 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Report this photo usage.
     * 
     * @param EntityManager $em entity manager
     * @param Person $person reporter
     * @param string $message content of the report
     */
    abstract public function report(EntityManager $em, Person $person, $message);

    abstract public function toArray();
}