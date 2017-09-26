<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation")
 * @ORM\Entity
 */
class Participation extends PhotoUsage
{    
    /**
     * @var unknown
     *
     * @ORM\ManyToOne(targetEntity="Battle", inversedBy="participations")
     * @ORM\JoinColumn(name="battle_id", referencedColumnName="id")
     */
    private $battle;
    
    /**
     * @var unknown
     *
     * @ORM\ManyToMany(targetEntity="LikeO")
     * @ORM\JoinTable(name="participation_like",
     *     joinColumns={@ORM\JoinColumn(name="participation_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="like_id", referencedColumnName="id", unique=true)}
     *     )
     */
    private $likes;
    
    /**
     * @var unknown
     *
     * @ORM\ManyToMany(targetEntity="Comment")
     * @ORM\JoinTable(name="participation_comment",
     *     joinColumns={@ORM\JoinColumn(name="participation_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
     *     )
     */
    private $comments;

    public function __construct()
    {
      $this->likes = new ArrayCollection();
      $this->comments = new ArrayCollection();
    }

    /**
     * Set battle.
     *
     * @param Battle $battle
     * @return Participation
     */
    public function setBattle(Battle $battle = null)
    {
        $this->battle = $battle;
    
        return $this;
    }

    /**
     * Get battle.
     *
     * @return Battle 
     */
    public function getBattle()
    {
        return $this->battle;
    }

    /**
     * Add a like.
     *
     * @param LikeO $likes
     * @return Participation
     */
    public function addLike(LikeO $like)
    {
        $this->likes[] = $like;
    
        return $this;
    }

    /**
     * Remove a like.
     *
     * @param LikeO $likes
     */
    public function removeLike(LikeO $like)
    {
        $this->likes->removeElement($like);
    }

    /**
     * Get likes.
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLikes()
    {
        return $this->likes;
    }
    
    /**
     * Get number of likes.
     * 
     * @return integer
     */
    public function getNumberLikes()
    {
        return $this->likes->count();
    }

    /**
     * Add a comment.
     *
     * @param Comment $comments
     * @return Participation
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    
        return $this;
    }

    /**
     * Remove a comment.
     *
     * @param Comment $comments
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments.
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Comment on a participation.
     * 
     * @param EntityManager $em entity manager
     * @param Person $person author of the new comment
     * @param string $message content of the new comment
     * @throws Exception
     * @return Comment
     */
    public function comment(EntityManager $em, Person $person, $message)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($person))
            throw new \Exception('Person is null');
        if (!is_string($message))
            throw new \Exception('Message is no string.');
        
        // create comment
        $comment = new Comment();
        $comment->setAuthor($person)
            ->setMessage($message);
        $em->persist($comment);
        
        // add it to the others
        $this->addComments($comment);
        
        // commit to database
        $em->flush();
        
        return $comment;
    }

    /**
     * Delete a comment on a participation.
     * 
     * @param EntityManager $em entity manager
     * @param Comment $comment comment to delete
     * @param Person $person person who is trying
     */
    public function deleteComment(EntityManager $em, Comment $comment, Person $person)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($comment))
            throw new \Exception('Comment is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        if ($comment->getAuthor() != $person)
            throw new \Exception('You are not the author.');
        
        // remove comment
        $this->removeComment($comment);
        $em->remove($comment);
        
        // commit to database
        $em->flush();
    }
    
    
    /**
     * Like a participation.
     * 
     * @param EntityManager $em entity manager
     * @param Person $person author of the like
     * @return LikeO
     */
    public function like(EntityManager $em, Person $person)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($person))
            throw new \Exception('Person is null');
        
        // create like
        $like = new LikeO();
        $like->setAuthor($person);
        $em->persist($like);
        
        // add it to the others
        $this->addLike($like);
        
        // commit to database
        $em->flush();
        
        return $like;
    }
    
    
    /**
     * Unlike a participation.
     * 
     * @param EntityManager $em entity manager
     * @param LikeO $like like to undo
     * @param Person $person person who is trying
     */
    public function unlike(EntityManager $em, LikeO $like, Person $person)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($like))
            throw new \Exception('Like is null.');
        if (is_null($person))
            throw new \Exception('Person is null.');
        if ($like->getAuthor() != $person)
            throw new \Exception('You are not the author.');
        
        // remove like
        $this->removeLike($like);
        $em->remove($like);
        
        // commit to database
        $em->flush();
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
        $report = new ParticipationReport();
        $report->setReporter($person)
            ->setParticipation($this)
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
            'battle' => $this->battle->getId(),
            'participant' => $this->participant->getId(),
            'photo' => $this->photo->getId(),
            'beganTime' => $this->beganTime
            );
    }
}