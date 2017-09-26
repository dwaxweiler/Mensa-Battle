<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Battle
 *
 * @ORM\Table(name="battle")
 * @ORM\Entity
 */
class Battle
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
     * @var unknown
     * 
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="datetime")
     */
    private $endTime;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="participationScore", type="integer")
     */
    private $participationScore;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToOne(targetEntity="Trophy")
     * @ORM\JoinColumn(name="trophy_id", referencedColumnName="id")
     */
    private $trophy;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="battle")
     */
    private $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();  
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
     * @return Battle
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
     * @param unknown $description
     * @return Battle
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }
    
    /**
     * Get description.
     * 
     * @return unknown
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startTime.
     *
     * @param \DateTime $startTime
     * @return Battle
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    
        return $this;
    }

    /**
     * Get startTime.
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime.
     *
     * @param \DateTime $endTime
     * @return Battle
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    
        return $this;
    }

    /**
     * Get endTime.
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set trophy.
     *
     * @param Trophy $trophy
     * @return Battle
     */
    public function setTrophy(Trophy $trophy = null)
    {
        $this->trophy = $trophy;
    
        return $this;
    }

    /**
     * Get trophy.
     *
     * @return Trophy 
     */
    public function getTrophy()
    {
        return $this->trophy;
    }

    /**
     * Add participations.
     *
     * @param Participation $participations
     * @return Battle
     */
    public function addParticipation(Participation $participation)
    {
        $this->participations[] = $participation;
    
        return $this;
    }

    /**
     * Remove participations.
     *
     * @param Participation $participations
     */
    public function removeParticipation(Participation $participation)
    {
        $this->participations->removeElement($participation);
    }

    /**
     * Get participations.
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getParticipations()
    {
        return $this->participations;
    }
    
    /**
     * Get number of the participations.
     * 
     * @return integer
     */
    public function getNumberParticipations()
    {
        return count($this->participations);
    }

    /**
     * Set participationScore.
     *
     * @param integer $participationScore
     * @return Battle
     */
    public function setParticipationScore($participationScore)
    {
        $this->participationScore = $participationScore;
    
        return $this;
    }

    /**
     * Get participationScore.
     *
     * @return integer 
     */
    public function getParticipationScore()
    {
        return $this->participationScore;
    }

    /**
     * Create a new battle.
     * 
     * @param EntityManager $em entity manager
     * @param string $title title of the new battle
     * @param \DateTime $start start time of the new battle
     * @param \DateTime $end end time of the new battle
     * @param integer $pscore participation score of the new battle
     * @param integer $tscore score of the trophy
     * @param string $ticonPath path to the trophy icon
     * @throws Exception
     * @return Battle
     */
    public static function create(EntityManager $em, Person $creator, $title, \DateTime $start, \DateTime $end, $pscore, $tscore, $ticonPath)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($start))
            throw new \Exception('Start time is null.');
        if (is_null($end))
            throw new \Exception('End time is null.');
        if (!is_string($title))
            throw new \Exception('Title is no string.');
        if (!is_int($pscore))
            throw new \Exception('Participation score is no integer.');
        if (!is_int($tscore))
            throw new \Exception('Trophy score is no integer.');
        if (!is_string($ticonPath))
            throw new \Exception('Trophy icon path is no string.');
        if (!$creator->getIsAdmin())
            throw new \Exception('You have not the rights.');
        
        $trophy = Trophy::create($em, $title, $tscore, $ticonPath);
        
        // create battle
        $battle = new Battle();
        $battle->setTitle($title)
            ->setStartTime($start)
            ->setEndTime($end)
            ->setTrophy($trophy)
            ->setParticipationScore($pscore);
        $em->persist($battle);
        
        // commit to database
        $em->flush();
        
        return $battle;
    }
    
    /**
     * Participate with a photo in this battle.
     * 
     * @param EntityManager $em entitiy manager
     * @param Person $person participator
     * @param Photo $Photo photo for the participation
     * @throws \Exception
     * @return Participation
     */
    public function participate(EntityManager $em, Person $person, Photo $photo)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        if (is_null($photo))
            throw new \Exception('Photo is null.');
        if ($this->endTime <= new \DateTime())
            throw new \Exception('The battle has already finished.');
        
        // create participation
        $participation = new Participation();
        $participation->setBattle($this)
            ->setParticipant($person)
            ->setPhoto($photo);
        $em->persist($participation);
        
        // add it to the others
        $this->addParticipation($participation);
      
        // commit to database
        $em->flush();
        
        return $participation;
    }
    
    /**
     * Widthdraw from this battle.
     * 
     * @param EntityManager $em entity manager
     * @param Participation $participation participation
     * @param Person $person person who is trying to withdraw
     * @throws \Exception
     */
    public function withdraw(EntityManager $em, Participation $participation, Person $person)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($participation))
            throw new \Exception('Participation is null.');
        if (is_null($person))
            throw new \Exception('Person is null');
        if ($participation->getParticipant() != $person)
            throw new \Exception('You are not the participator.');
        if ($this->endTime <= new \DateTime())
            throw new \Exception('The battle has already finished.');
        
        // remove participation
        $this->removeParticipation($participation);
        $em->remove($participation);
        
        // commit to database
        $em->flush();
    }
    
    /**
     * Determine the winner of this battle. If there is more than one winner, return a random one.
     * 
     * @param EntityManager $em entity manager
     * @throws \Exception
     * @return Ambigous <mixed>
     */
    public function determineWinner(EntityManager $em)
    {
        if ($this->endTime > new \DateTime())
            throw new \Exception('The battle has not finished yet.');
        
        $counts = array();
        foreach ($this->participations as $participation)
            $counts[$participation->getId()] = $participation->getNumberLikes();
        arsort($counts);

        $winPartId = key($counts);
        $winPartLikes = current($counts);
        next($counts);
        $winners = array($winPartId);
        while (current($counts) == $winPartLikes)
        {
            $winners[] = key($counts);
            next($counts);
        }
        
        // randomize winner if there are more
        $winner = $em->getRepository('MensaBattleAPIBundle:Participation')
            ->find($winners[rand(0, count($counts)-1)]);
        
        // transfer trophy and set time
        $winner->addTrophy($this->trophy);
        $this->trophy->setReceivedTime(new \DateTime());
        
        return $winner;
    }
    
    public function toArray()
    {
        $output = array();
        foreach ($this->participations as $participation)
            $output[] = $participation->getId();
        
        return array('id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'startTime' => $this->startTime->format('Y-m-d H:i:s'),
            'endTime' => $this->endTime->format('Y-m-d H:i:s'),
            'participationScore' => $this->participationScore,
            'trophyId' => $this->trophy->getId(),
            'participations' => $output
        );
    }
}
