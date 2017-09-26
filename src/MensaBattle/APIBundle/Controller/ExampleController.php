<?php

namespace MensaBattle\APIBundle\Controller;

use MensaBattle\APIBundle\Entity\Example;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExampleController extends Controller
{
    /**
     * Create a report on an example photo.
     * 
     * @param integer $id example id
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
        $example = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Example')
            ->find($id);
        if (!$example)
            throw $this->createNotFoundException('No example photo found for id '.$id);
        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        
        $report = $example->report($this->getDoctrine()->getEntityManager(), $person, $params['message']);

        // respond
        $response = new JsonResponse();
        $response->setData($report->toArray());
        return $response;
    }
    
    /**
     * Get a report on an example photo.
     * 
     * @param integer $id report id
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getReportAction($id)
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
        
        // retrieve report
        $report = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:ExampleReport')
            ->find($id);
        if (!$report)
            throw $this->createNotFoundException('No report found for id '.$id);
        
        // respond
        $response = new JsonResponse();
        $response->setData($report->toArray());
        return $response;
    }
    
    /**
     * Get all the reports on all example photos.
     * 
     * @throws \Exception
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
            ->getRepository('MensaBattleAPIBundle:ExampleReport')
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
