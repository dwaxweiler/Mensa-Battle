<?php

namespace MensaBattle\APIBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Person implements UserInterface, \Serializable
{
    private static $SCORE_EXAMPLE_PHOTO = 30;
    private static $SCORE_RATING = 10;
    
    /**
     * @var string $id facebook user id
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string $fbLink
     *
     * @ORM\Column(name="fbLink", type="string")
     */
    private $fbLink;

    /**
     * @var boolean $isAdmin
     *
     * @ORM\Column(name="isAdmin", type="boolean")
     */
    private $isAdmin;
    
    /**
     * @var \DateTime $joinedTime
     * 
     * @ORM\Column(name="joinedTime", type="datetime")
     */
    private $joinedTime;

    /**
     * @var unknown
     * 
     * @ORM\OneToOne(targetEntity="Album", mappedBy="owner")
     */
    private $album;
    
    /**
     * @var unknown
     * 
     * @ORM\ManyToMany(targetEntity="Trophy")
     * @ORM\JoinTable(name="person_trophy",
     *     joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="trophy_id", referencedColumnName="id", unique=true)}
     *     )
     */
    private $trophies;

    
    public function __construct()
    {
        $this->trophies = new ArrayCollection();
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
     * Set id.
     *
     * @param integer $id
     * @return Person
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     * @return Person
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name.
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fbLink.
     *
     * @param string $fbLink
     * @return Person
     */
    public function setFbLink($fbLink)
    {
        $this->fbLink = $fbLink;
    
        return $this;
    }

    /**
     * Get fbLink.
     *
     * @return string 
     */
    public function getFbLink()
    {
        return $this->fbLink;
    }

    /**
     * Set isAdmin.
     *
     * @param boolean $isAdmin
     * @return Person
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    
        return $this;
    }

    /**
     * Get isAdmin.
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set time when the person is first persisted.
     * 
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->joinedTime = new \DateTime();
    }
    
    /**
     * Get joinedTime.
     * 
     * @return \DateTime
     */
    public function getJoinedTime()
    {
        return $this->joinedTime;
    }

    /**
     * Set album.
     *
     * @param Album $album
     * @return Person
     */
    public function setAlbum(Album $album = null)
    {
        $this->album = $album;
    
        return $this;
    }

    /**
     * Get album.
     *
     * @return Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Add trophy.
     *
     * @param Trophy $trophies
     * @return Person
     */
    public function addTrophy(Trophy $trophies)
    {
        $this->trophies[] = $trophies;
    
        return $this;
    }

    /**
     * Remove trophy.
     *
     * @param Trophy $trophies
     */
    public function removeTrophy(Trophy $trophies)
    {
        $this->trophies->removeElement($trophies);
    }

    /**
     * Get trophies.
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTrophies()
    {
        return $this->trophies;
    }
    
    /**
     * Get number of trophies.
     * 
     * @return number
     */
    public function getNumberTrophies()
    {
        return count($this->trophies);
    }
    
    /**
     * Calulate and return the total score of this person.
     * 
     * @param EntityManager $em entity manager
     * @return number
     */
    public function calculateTotalScore(EntityManager $em)
    {
        $sum = 0;
        
        // trophies
        foreach ($this->trophies as $trophy)
            $sum += $trophy->getScore();
        
        // participations
        $query = $em
            ->createQuery('SELECT p
                FROM MensaBattleAPIBundle:Participation p
                JOIN p.participant u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $participations = $query->getResult();
        // - calculate
        foreach ($participations as $participation)
            $sum += $participation->getBattle()->getParticipationScore();
        
        // example photos
        $query = $em
            ->createQuery('SELECT e
                FROM MensaBattleAPIBundle:Example e
                JOIN e.participant u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $examples = $query->getResult();
        // - calculate
        foreach ($examples as $example)
            $sum += Person::$SCORE_EXAMPLE_PHOTO;
                
        // ratings
        $query = $em
            ->createQuery('SELECT r
                FROM MensaBattleAPIBundle:Rating r
                JOIN r.author u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $ratings = $query->getResult();
        // - calculate
        foreach ($ratings as $rating)
            $sum += Person::$SCORE_RATING;
        
        return $sum;
    }
    
    /**
     * Calculate and return the score made in the last week of this person.
     * 
     * @param EntityManager $em entity manager
     * @return number
     */
    public function calculateLastWeekScore(EntityManager $em)
    {
        $sum = 0;
        
        // set datetime before one week and only add scores of what happened last week
        $week = new \DateTime();
        $week->sub(new \DateInterval('P7D'));
        
        // trophies
        foreach ($this->trophies as $trophy)
            if (!is_null($trophy->getReceivedTime()) && $trophy->getReceivedTime() > $week)
                $sum += $trophy->getScore();
        
        // participations
        $query = $em
            ->createQuery('SELECT p
                FROM MensaBattleAPIBundle:Participation p
                JOIN p.participant u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $participations = $query->getResult();
        // - calculate
        foreach ($participations as $participation)
            if ($participation->getBeganTime() > $week)
                $sum += $participation->getBattle()->getParticipationScore();
        
        // example photos
        $query = $em
            ->createQuery('SELECT e
                FROM MensaBattleAPIBundle:Example e
                JOIN e.participant u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $examples = $query->getResult();
        // - calculate
        foreach ($examples as $example)
            if ($example->getBeganTime() > $week)
                $sum += Person::$SCORE_EXAMPLE_PHOTO;
                
        // ratings
        $query = $em
            ->createQuery('SELECT r
                FROM MensaBattleAPIBundle:Rating r
                JOIN r.author u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $ratings = $query->getResult();
        // - calculate
        foreach ($ratings as $rating)
            if ($rating->getTime() > $week)
                $sum += Person::$SCORE_RATING;
        
        return $sum;
    }
    
    /**
     * Calculate and return the score made in the last week of this person.
     * 
     * @param EntityManager $em entity manager
     * @return number
     */
    public function calculateLastMonthScore(EntityManager $em)
    {
        $sum = 0;
        
        // set datetime before one week and only add scores of what happened last week
        $month = new \DateTime();
        $month->sub(new \DateInterval('P1M'));
        
        // trophies
        foreach ($this->trophies as $trophy)
            if (!is_null($trophy->getReceivedTime()) && $trophy->getReceivedTime() > $month)
                $sum += $trophy->getScore();
        
        // participations
        $query = $em
            ->createQuery('SELECT p
                FROM MensaBattleAPIBundle:Participation p
                JOIN p.participant u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);        
        $participations = $query->getResult();
        // - calculate
        foreach ($participations as $participation)
            if ($participation->getBeganTime() > $month)
                $sum += $participation->getBattle()->getParticipationScore();
        
        // example photos
        $query = $em
            ->createQuery('SELECT e
                FROM MensaBattleAPIBundle:Example e
                JOIN e.participant u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $examples = $query->getResult();
        // - calculate
        foreach ($examples as $example)
            if ($example->getBeganTime() > $month)
                $sum += Person::$SCORE_EXAMPLE_PHOTO;
                
        // ratings
        $query = $em
            ->createQuery('SELECT r
                FROM MensaBattleAPIBundle:Rating r
                JOIN r.author u
                WHERE u.id = :id')
            ->setParameter('id', $this->id);
        $ratings = $query->getResult();
        // - calculate
        foreach ($ratings as $rating)
            if ($rating->getTime() > $month)
                $sum += Person::$SCORE_RATING;
        
        return $sum;
    }
    
    /**
     * Register a new person.
     * 
     * @param EntityManager $em entity manager
     * @param string $id user id
     * @param string $name user name
     * @param string $fbLink link to facebook profile
     * @param boolean $isAdmin wether this user is admin or not
     * @throws \Exception
     * @return Person
     */
    public static function register(EntityManager $em, $id, $name, $fbLink, $isAdmin = false)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (!is_string($id))
            throw new \Exception('Id is no string.');
        if (!is_string($name))
            throw new \Exception('Name is no string.');
        if (!is_string($fbLink))
            throw new \Exception('Facebook link is no string.');
        if (!is_bool($isAdmin))
            throw new \Exception('isAdmin is no boolean.');
        
        // create person
        $person = new Person();
        $person->setFbLink($fbLink)
            ->setId($id)
            ->setIsAdmin($isAdmin)
            ->setName($name);
        $em->persist($person);

        // create own album
        $album = new Album();
        $album->setOwner($person);
        $person->setAlbum($album);
        $em->persist($album);
        
        // commit to database
        $em->flush();
        
        return $person;
    }
    
    public function toArray()
    {
        $trophiesArray = array();
        foreach ($this->trophies as $trophy)
            $trophiesArray[] = $trophy->toArray();

        return array('id' => $this->id,
            'name' => $this->name,
            'link' => $this->fbLink,
            'joinedTime' => $this->joinedTime->format('Y-m-d H:i:s'),
            'albumId' => $this->album?$this->album->getId():'',
            'trophies' => $trophiesArray);
    }
    
    public function setFBData($fbdata)
    {
        if (isset($fbdata['id']))
            $this->setId($fbdata['id']);
        if (isset($fbdata['name']))
            $this->setName($fbdata['name']);
        if (isset($fbdata['link']))
            $this->setFbLink($fbdata['link']);
        $this->setIsAdmin(false);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        if ($this->isAdmin)
            return array('ROLE_ADMIN');
        else
            return array('ROLE_USER');
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        // do nothing
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
                $this->id
        ));
    }
    
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
                $this->id
        ) = unserialize($serialized);
    }
}
