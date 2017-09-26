<?php

namespace MensaBattle\APIBundle\Controller;

use MensaBattle\APIBundle\Service\FacebookConnector;
use MensaBattle\APIBundle\Entity\Participation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ParticipationController extends Controller
{
    /**
     * Create a comment on a participation.
     * 
     * @param integer $id participation id
     * @throws \Exception
     */
    public function createCommentAction($id)
    {
        $params = array();
        $content = $this->getRequest()->getContent();
        if (!empty($content))
            $params = json_decode($content, true);

        // check if parameters are set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
        if (empty($params['message']))
            throw new \Exception('No message.');
      
        // retrieve objects
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($id);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$id);
        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
      
        $comment = $participation->comment($this->getDoctrine()->getEntityManager(), $person, $params['message']);

        // respond
        $response = new JsonResponse();
        $response->setData($comment->toArray());
        return $response;
    }
    
    
    /**
     * Delete a comment on a participation.
     * 
     * @param integer $pid participation id
     * @param integer $cid comment id
     * @throws \Exception
     */
    public function deleteCommentAction($pid, $cid)
    {
        // check if fbtoken are set
        if (!$request->cookies->has('fbtoken'))
            throw new \Exception('No facebook token.');
        
        // retrieve objects
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($pid);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$pid);
        $comment = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Comment')
            ->find($cid);
        if (!$comment)
            throw $this->createNotFoundException('No comment found for id '.$cid);
        $personId = $this->get('facebook_connector')
            ->getUserId($request->cookies->get('fbtoken'));
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        
        $participation->deleteComment($this->getDoctrine()->getEntityManager(), $comment, $person);
    }
    
    
    /**
     * Get a comment on a participation.
     * 
     * @param integer $id comment id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCommentAction($id)
    {
        // retrieve comment
        $comment = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Comment')
            ->find($id);
        if (!$comment)
            throw $this->createNotFoundException('No comment found for id '.$id);
        
        // respond
        $response = new JsonResponse();
        $response->setData($comment->toArray());
        return $response;
    }
    
    
    /**
     * Get all the comments on a participation.
     * 
     * @param integer $id participation id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCommentsAction($id)
    {
        // retrieve participation
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($id);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$id);
        
        // bundle comments
        $commentsArray = array();
        foreach ($participation->getComments() as $comment)
            $commentsArray[] = $comment->toArray();

        // respond
        $response = new JsonResponse();
        $response->setData($commentsArray);
        return $response;
    }
    
    
    /**
     * Create a like on a participation.
     * 
     * @param integer $id participation id
     * @throws \Exception
     */
    public function createLikeAction($id)
    {
        $params = array();
        $content = $this->getRequest()->getContent();
        if (!empty($content))
            $params = json_decode($content, true);
      
        // check if fbtoken is set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
      
        // retrieve objects
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($id);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$id);
        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
      
        $like = $participation->like($this->getDoctrine()->getEntityManager(), $person);

        // respond
        $response = new JsonResponse();
        $response->setData($like->toArray());
        return $response;
    }
    
    
    /**
     * Delete a like on a participation.
     * 
     * @param integer $pid participation id
     * @param integer $lid like id
     * @throws \Exception
     */
    public function deleteLikeAction($pid,$lid)
    {
        $request = $this->getRequest();
      
        // check if fbtoken is set
        if (!$request->cookies->has('fbtoken'))
            throw new \Exception('No facebook token.');
        
        // retrieve objects
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($pid);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$pid);
        $like = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:LikeO')
            ->find($lid);
        if (!$like)
            throw $this->createNotFoundException('No like found for id '.$lid);
        $personId = $this->get('facebook_connector')
            ->getUserId($request->cookies->get('fbtoken'));
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        
        $participation->deleteLike($this->getDoctrine()->getEntityManager(), $like, $person);
    }
    
    
    /**
     * Get a like on a participation.
     * 
     * @param integer $id like id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLikeAction($id)
    {
        // retrieve like
        $like = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:LikeO')
            ->find($id);
        if (!$like)
            throw $this->createNotFoundException('No like found for id '.$id);
        
        // respond
        $response = new JsonResponse();
        $response->setData($like->toArray());
        return $response;
      
    }
    
    
    /**
     * Get all the likes on a participation.
     * 
     * @param integer $id participation id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLikesAction($id)
    {
        // retrieve participation
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($id);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$id);
        
        // bundle likes
        $likesArray = array();
        foreach ($participation->getLikes() as $like)
            $likesArray[] = $like->toArray();

        // respond
        $response = new JsonResponse();
        $response->setData($likesArray);
        return $response;
    }
    
    
    /**
     * Create a report on a participation.
     * 
     * @param integer $id participation id
     * @throws \Exception
     */
    public function createReportAction($id)
    {
        $params = array();
        $content = $this->getRequest()->getContent();
        if (!empty($content))
            $params = json_decode($content, true);
        
        // check if parameters are set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
        if (empty($params['message']))
            throw new \Exception('No message.');
        
        // retrieve objects
        $participation = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Participation')
            ->find($id);
        if (!$participation)
            throw $this->createNotFoundException('No participation found for id '.$id);
        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        
        $report = $participation->report($this->getDoctrine()->getEntityManager(), $person, $params['message']);

        // respond
        $response = new JsonResponse();
        $response->setData($report->toArray());
        return $response;
    }
    
    /**
     * Get a report on a participation.
     * 
     * @param integer $id report id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getReportAction($id)
    {
        $request = $this->getRequest();
      
        // check parameters
        if (!$request->cookies->has('fbtoken'))
            throw new \Exception('No facebook token.');
        $user = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($this->get('facebook_connector')->getUserId($request->cookies->get('fbtoken')));
        if (!$user->getIsAdmin())
            throw new \Exception('You have not the rights.');
        
        // retrieve report
        $report = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:ParticipationReport')
            ->find($id);
        if (!$report)
            throw $this->createNotFoundException('No report found for id '.$id);
        
        // respond
        $response = new JsonResponse();
        $response->setData($report->toArray());
        return $response;
    }
    
    
    /**
     * Get all the reports on all participations.
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getReportsAction()
    {
        $request = $this->getRequest();
      
        // check if fbtoken is set
        if (!$request->cookies->has('fbtoken'))
            throw new \Exception('No facebook token.');
        $user = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($this->get('facebook_connector')->getUserId($request->cookies->get('fbtoken')));
        if (!$user->getIsAdmin())
            throw new \Exception('You have not the rights.');
        
        // retrieve reports
        $reports = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:ParticipationReport')
            ->findAll();
        
        // bundle reports
        $reportsArray = array();
        foreach ($reports as $report)
          $reportsArray[] = $report->toArray();
        
        // respond
        $response = new JsonResponse();
        $response->setData($reportsArray);
        return $response;
    }
}
