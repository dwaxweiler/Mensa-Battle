<?php

namespace MensaBattle\APIBundle\Controller;

use MensaBattle\APIBundle\Entity\Battle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class BattleController extends Controller
{
    /**
     * Create a new battle including the trophy for the winner using POST variables.
     * 
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction()
    {
       $params = array();
       $content = $this->getRequest()->getContent();
       if (!empty($content))
           $params = json_decode($content, true);

        // check if parameters are set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
        if (empty($params['title']))
            throw new \Exception('No title.');
        if (empty($params['start']))
            throw new \Exception('No start time.');
        if (empty($params['end']))
            throw new \Exception('No end time.');
        if (empty($params['pscore']))
            throw new \Exception('No participation score.');
        if (empty($params['tscore']))
            throw new \Exception('No trophy score.');
        if (empty($params['ticon']))
            throw new \Exception('No trophy icon.');

        // store trophy icon
        $trophyIcon = $params['ticon'];
        $iconPath = $_SERVER['DOCUMENT_ROOT'].'/'.$_SERVER['HTTP_HOST'].'/web/images/trophies/'.md5(time());
        move_uploaded_file($trophyIcon['tmp_name'], $iconPath);

        $battle = Battle::create($this->getDoctrine()->getEntityManager(),
            $params['fbtoken'],
            $params['title'],
            $params['start'],
            $params['end'],
            $params['pscore'],
            $params['tscore'],
            $iconPath);

        //respond
        $response = new JsonResponse();
        $response->setData($battle->toArray());
        return $response;
    }
    
    /**
     * Get the details of a battle by GET request.
     * 
     * @param integer $id battle id 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAction($id)
    {
        // retrieve battle
        $battle = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Battle')
            ->find($id);
        if (!$battle)
            throw $this->createNotFoundException('No battle found for id '.$id);
        
        // respond
        $response = new JsonResponse();
        $response->setData($battle->toArray());
        return $response;
    }
    
    /**
     * Get the running battles with their details by GET request.
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRunningAction()
    {        
        // retrieve battles
        $em = $this->getDoctrine()
            ->getManager();
        $query = $em->createQuery(
            'SELECT b
            FROM MensaBattleAPIBundle:Battle b
            WHERE b.endTime >= CURRENT_TIMESTAMP()
            ORDER BY b.endTime ASC');
        $battles = $query->getResult();
        
        // bundle battles
        $battlesArray = array();
        foreach ($battles as $battle)
            $battlesArray[] = $battle->toArray();
        
	    	// respond
        $response = new JsonResponse();
        $response->setData($battlesArray);
        return $response;
    }
    
    /**
     * Get the past battles with their details by GET request.
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getPastAction()
    {        
        // retrieve battles
        $em = $this->getDoctrine()
            ->getManager();
        $query = $em->createQuery(
            'SELECT b
            FROM MensaBattleAPIBundle:Battle b
            WHERE b.endTime < CURRENT_TIMESTAMP()
            ORDER BY b.endTime ASC');
        $battles = $query->getResult();
        
        // bundle battles
        $battlesArray = array();
        foreach ($battles as $battle)
            $battlesArray[] = $battle->toArray();
        
	    	// respond
        $response = new JsonResponse();
        $response->setData($battlesArray);
        return $response;
    }
    
    /**
     * Get all the battles with their details by GET request.
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllAction()
    {        
        // retrieve battles
        $battles = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Battle')
            ->findAll();
        
        // bundle battles
        $battlesArray = array();
        foreach ($battles as $battle)
            $battlesArray[] = $battle->toArray();
        
	    	// respond
        $response = new JsonResponse();
        $response->setData($battlesArray);
        return $response;
    }
    
    /**
     * Create a photo participation of a person in a battle using POST variables.
     * 
     * @param integer $id battle id
     * @throws \Exception
     */
    public function createParticipationAction($id)
    {
       $params = array();
       $content = $this->getRequest()->getContent();
       if (!empty($content))
           $params = json_decode($content, true);
      
        // check if parameters are set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
        if (empty($params['photoId']))
            throw new \Exception('No photo id.');
      
        // retrieve objects
        $battle = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Battle')
            ->find($id);
        if (!$battle)
            throw $this->createNotFoundException('No battle found for id '.$id);
        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        $photoId = $params['photoId'];
        $photo = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Photo')
            ->find($photoId);
        if (!$photo)
            throw $this->createNotFoundException('No photo found for id '.$photoId);
        
        $participation = $battle->participate($this->getDoctrine()->getEntityManager(), $person, $photo);

        //respond
        $response = new JsonResponse();
        $response->setData($participation->toArray());
        return $response;
    }
    
    /**
     * Get the details of a participation by a GET request.
     * 
     * @param integer $id participation id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getParticipationAction($id)
    {
        // retrieve participation
        $example = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($id);
        if (!$example)
            throw $this->createNotFoundException('No participation found for id '.$id);
        
        // respond
        $response = new JsonResponse();
        $response->setData($example->toArray());
        return $response;
    }

    /**
     * Get the details of all the participations on a battle by a GET request.
     * 
     * @param integer $id battle id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getParticipationsAction($id)
    {
        // retrieve battle
        $battle = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Battle')
            ->find($id);
        if (!$battle)
            throw $this->createNotFoundException('No battle found for id '.$id);
        
        // get participations
        $participationsArray = array();
        foreach ($battle->getParticipations() as $participation)
            $participationsArray[] = $participation->toArray();
        
        // respond
        $response = new JsonResponse();
        $response->setData($participationsArray);
        return $response;
    }
    
    /**
     * Withdraw from a battle by deleting the participation using POST variable.
     * 
     * @param integer $bid battle id
     * @param integer $pid participation id
     * @throws \Exception
     */
    public function deleteParticipationAction($bid, $pid)
    {
        $request = $this->getRequest();
        
        // check if fbtoken is set
        if (!$request->cookies->has('fbtoken'))
            throw new \Exception('No facebook token.');
        
        // retrieve objects
        $personId = $this->get('facebook_connector')
            ->getUserId($request->cookies->get('fbtoken'));
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        $battle = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Battle')
            ->find($bid);
        if (!$battle)
            throw $this->createNotFoundException('No battle found for id '.$bid);
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($pid);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$pid);
        
        $battle->withdraw($this->getDoctrine()->getEntityManager(), $participation, $person);
    }
}
