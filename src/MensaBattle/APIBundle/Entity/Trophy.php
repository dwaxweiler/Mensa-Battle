<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trophy
 *
 * @ORM\Table(name="trophy")
 * @ORM\Entity
 */
class Trophy
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
     * @ORM\Column(name="iconPath", type="string")
     */
    private $iconPath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="receivedTime", type="datetime", nullable=true)
     */
    private $receivedTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;


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
     * Set title
     *
     * @param string $title
     * @return Trophy
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set iconPath
     *
     * @param string $iconPath
     * @return Trophy
     */
    public function setIconPath($iconPath)
    {
        $this->iconPath = $iconPath;
    
        return $this;
    }

    /**
     * Get iconPath
     *
     * @return string 
     */
    public function getIconPath()
    {
        return $this->iconPath;
    }

    /**
     * Set receivedTime
     *
     * @param \DateTime $receivedTime
     * @return Trophy
     */
    public function setReceivedTime($receivedTime)
    {
        $this->receivedTime = $receivedTime;
    
        return $this;
    }

    /**
     * Get receivedTime
     *
     * @return string 
     */
    public function getReceivedTime()
    {
        return $this->receivedTime;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Trophy
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }
    
    /**
     * Create a new trophy.
     * 
     * @param EntityManager $em entity manager
     * @param string $title title of the new trophy
     * @param integer $score score of the new trophy
     * @param string $iconPath path to the icon of the new trophy
     * @throws Exception
     * @return Trophy
     */
    public static function create(EntityManager $em, $title, $score, $iconPath)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if(!is_string($title))
            throw new \Exception('Title is no string.');
        if(!is_int($score))
            throw new \Exception('Score is no integer.');
        if(!is_string($iconPath))
            throw new \Exception('Icon path is no string.');
        
        // create trophy
        $trophy = new Trophy();
        $trophy->setTitle($title)
            ->setScore($score)
            ->setIconPath($iconPath);
        $em->persist($trophy);
        
        // commit to database
        $em->flush();
        
        return $trophy;
    }
    
    public function toArray()
    {
        return array('id' => $this->id,
            'title' => $this->title,
            'iconPath' => $this->iconPath,
            'receivedTime' => $this->receivedTime->format('Y-m-d H:i:s'),
            'score' => $this->score);
    }
}