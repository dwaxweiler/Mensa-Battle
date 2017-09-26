<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Photo album of a user
 *
 * @ORM\Table(name="album")
 * @ORM\Entity
 */
class Album
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
     * @ORM\OneToOne(targetEntity="Person", inversedBy="album")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="album")
     */
    private $photos;
    
    
    public function __construct()
    {
        $this->products = new ArrayCollection();
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
     * Set owner.
     *
     * @param Person $owner
     * @return Album
     */
    public function setOwner(Person $owner)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner.
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add photo.
     *
     * @param Photo $photo
     * @return Album
     */
    public function addPhoto(Photo $photo)
    {
        $this->photos[] = $photo;
    
        return $this;
    }

    /**
     * Remove photo.
     *
     * @param Photo $photo
     */
    public function removePhoto(Photo $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos.
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Create a new photo.
     * 
     * @param EntityManager $em entity manager
     * @param string $photoPath path to the new photo
     * @throws \Exception
     * @return Photo
     */
    public function createPhoto(EntityManager $em, $photoPath)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (!is_string($photoPath))
            throw new \Exception('Photo path is no string.');
        
        // create photo
        $photo = new Photo();
        $photo->setAlbum($this)
            ->setFilePath($photoPath)
            ->setOwner($this->owner);
        $em->persist($photo);
        
        // add to others
        $this->addPhoto($photo);
        
        // commit to database
        $em->flush();
        
        return $photo;
    }

    /**
     * Delete a photo.
     * 
     * @param EntityManager $em entity manager
     * @param Photo $photo photo to delete
     */
    public function deletePhoto(EntityManager $em, Photo $photo)
    {
        if (is_null($em))
            throw new \Exception('Entity manager is null.');
        if (is_null($photo))
            throw new \Exception('Photo is null.');
        // remove photo
        $this->removePhoto($photo);
        $em->remove($photo);
        
        // commit to database
        $em->flush();
    }
}
