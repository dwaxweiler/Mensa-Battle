<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Report
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
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    protected $message;
    
    /**
     * @var unknown
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $reporter;

    /**
     * @var \DateTime $time
     *
     * @ORM\Column(name="time", type="datetime")
     */
    protected $time;

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
     * Set message.
     *
     * @param string $message
     * @return Report
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
     * Set reporter.
     *
     * @param Person $reporter
     * @return Report
     */
    public function setReporter(Person $reporter = null)
    {
        $this->reporter = $reporter;
    
        return $this;
    }

    /**
     * Get reporter.
     *
     * @return Person 
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set time when the report is first persisted.
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

    abstract public function toArray();
}