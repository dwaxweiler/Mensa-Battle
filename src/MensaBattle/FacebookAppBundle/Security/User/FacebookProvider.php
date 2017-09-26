<?php

namespace MensaBattle\FacebookAppBundle\Security\User;

use \BaseFacebook;
use \FacebookApiException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use MensaBattle\APIBundle\Entity\Album;
use MensaBattle\APIBundle\Entity\Person;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookProvider implements UserProviderInterface
{
    protected $facebook;
    
  	/**
  	 * Entity manager
  	 * 
  	 * @var EntityManager
  	 */
  	protected $em;
  	
    protected $validator;
  	
  	/**
  	 * Logger
  	 * 
  	 * @var Logger
  	 */
  	private $logger;
    
    public function __construct(BaseFacebook $facebook, EntityManager $em, $validator, Logger $logger)
    {
        $this->facebook = $facebook;
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
        
        $this->logger->debug('FacebookProvider created!');
    }
    
    public function loadUserByUsername($username)
    {
        $this->logger->debug('Load user by username: '.$username);
        
        // query user
        $query = $this->em->createQuery(
            'SELECT p
            FROM MensaBattleAPIBundle:Person p
            WHERE p.id = :id')
            ->setParameter('id', $username);
        try
        {
            $person = $query->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $person = null;

            $this->logger->debug('No user found in the database.');
        }
        
//         if (!$person)
//         	throw $this->createNotFoundException('No person found for $id: '.$id);
        
        if (is_null($person))
        {
            $this->logger->debug('Try to get her data from Facebook.');
            
            try
            {
                $fbdata = $this->facebook->api('/me', 'GET');
            }
            catch (FacebookApiException $e)
            {
                $fbdata = null;
    
                $this->logger->debug('No data received from facebook: '.$e->getType().' - '.$e->getMessage());
            }
        }
    
        if (isset($fbdata) && !empty($fbdata))
        {
            if (empty($person))
            {
                $this->logger->debug('Create user because she does not exist yet.');
                
                // create user because she does not exist
                $person = new Person();
                $person->setId($username);
                $this->em->persist($person);

                // create own album
                $album = new Album();
                $album->setOwner($person);
                $person->setAlbum($album);
                $this->em->persist($album);
            }
            
            // set data
            $person->setFBData($fbdata);
            
            // verify whether or not the data satisfies the constraints
            if (count($this->validator->validate($person)))
            {
                $this->logger->debug('The data could not be validated');
                
                // TO DO: the user was found obviously, but does not match our expectations, do something smart
                throw new UsernameNotFoundException('The facebook user could not be stored.');
            }
            else
            {
                $this->logger->debug('Commit user (changes) to database.');
                
                // commit changes to database
                $this->em->flush();
            }
        }
        
        if (empty($person))
            throw new UsernameNotFoundException('The user is not authenticated on facebook.');
        
        return $person;
    }
    
    public function refreshUser(UserInterface $user)
    {
        $this->logger->debug('refreshUser() is called.');
        
        if (!$this->supportsClass(get_class($user)) || !$user->getId())
            throw new UnsupportedUserException(sprintf('Instances of %s are not supported.', get_class($user)));
        
        return $this->loadUserByUsername($user->getId());
    }
    
    public function supportsClass($class)
    {
        $this->logger->debug('supportsClass() is called.');
        
        return $class === 'MensaBattle\APIBundle\Entity\Person';
    }
}
