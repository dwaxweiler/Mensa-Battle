<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Photo
{
    /**
    * @var integer
    * 
    * @ORM\Id
    * @ORM\Column(name="id", type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;
  		
  	/**
    * @var unknown
    * 
  	* @ORM\ManyToOne(targetEntity="Album", inversedBy="photos")
  	* @ORM\JoinColumn(name="album_id", referencedColumnName="id")
  	*/
  	private $album;
  		
  	/**
    * @var string
    * 
  	* @ORM\Column(name="filePath", type="string")
  	*/
  	private $filePath;
  		
  	/**
    * @var datetime
    * 
  	* @ORM\Column(name="uploadTime", type="datetime")
  	*/
    private $uploadTime;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $owner;

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
     * @param string $owner
     * @return Photo
     */
    public function setOwner($owner)
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
     * Set filePath.
     *
     * @param string $filePath
     * @return Photo
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    
        return $this;
    }

    /**
     * Get filePath.
     *
     * @return string 
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set album.
     *
     * @param Album $album
     * @return Photo
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
     * Set time when the photo is first persisted.
     * 
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->uploadTime = new \DateTime();
    }

    /**
     * Get uploadTime.
     *
     * @return \DateTime 
     */
    public function getUploadTime()
    {
        return $this->uploadTime;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'owner' => $this->owner->getId(),
            'uploadTime' => $this->uploadTime->format('Y-m-d H:i:s'),
            'filePath' => $this->filePath,
            'albumId' => $this->album?$this->album->getId():''
        );
    }
}
