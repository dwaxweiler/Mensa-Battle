<?php

namespace MensaBattle\APIBundle\Controller;

use MensaBattle\APIBundle\Service\FacebookConnector;
use MensaBattle\APIBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class PersonController extends Controller
{
    /**
     * User login. Register a new person if a user logs in the first time.
     *
     * @throws \Exception
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
        if (empty($params['id']))
            throw new \Exception('No id.');
        if (empty($params['name']))
            throw new \Exception('No name.');
        if (empty($params['link']))
            throw new \Exception('No link to the facebook profile.');
        if ($this->get('facebook_connector')->getUserId($params['fbtoken']) != $params['id'])
            throw new \Exception('Facebook token and id do not match.');

        $person = $this->getDoctrine()->getRepository('MensaBattleAPIBundle:Person')->find($params['id']);
        if (!$person)
            $person = Person::register($this->getDoctrine()->getEntityManager(), $params['id'], $params['name'], $params['link']);

        // respond
        $response = new JsonResponse();
        $response->setData($person->toArray());
        return $response;
    }

    /**
     * Get a user.
     * 
     * @param string $id user id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getAction($id)
    {
        // retrieve person
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($id);
        if (!$person)
            throw $this->createNotFoundException('No person found for $id: '.$id);
        
        // respond
        $response = new JsonResponse();
        $responseArray = $person->toArray();
        $responseArray['totalScore'] = $person->calculateTotalScore($this->getDoctrine()->getEntityManager());
        $responseArray['lastWeekScore'] = $person->calculateLastWeekScore($this->getDoctrine()->getEntityManager());
        $responseArray['lastMonthScore'] = $person->calculateLastMonthScore($this->getDoctrine()->getEntityManager());
        $response->setData($responseArray);
        return $response;
    }
    
    /**
     * Get all the users.
     * 
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getAllAction()
    {
        // retrieve all persons
        $persons = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->findAll();
        
        // bundle persons
        $responseArray = array();
        foreach ($persons as $person) {
            $personsArray = $person->toArray();
            $personsArray['totalScore'] = $person->calculateTotalScore($this->getDoctrine()->getEntityManager());
            $personsArray['lastWeekScore'] = $person->calculateLastWeekScore($this->getDoctrine()->getEntityManager());
            $personsArray['lastMonthScore'] = $person->calculateLastMonthScore($this->getDoctrine()->getEntityManager());
            $responseArray[] = $personsArray;
        }
        
        // respond
        $response = new JsonResponse();
        $response->setData($responseArray);
        return $response;
    }
}
