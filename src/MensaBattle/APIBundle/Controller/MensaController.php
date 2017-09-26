<?php

namespace MensaBattle\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\ResultSetMapping;

class MensaController extends Controller
{
    /**
     * Get the details of a mensa by GET request.
     * 
     * @param integer $id mensa id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getAction($id)
    {
        // retrieve mensa
        $mensa = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Mensa')
            ->find($id);
        if(!$mensa)
            throw $this->createNotFoundException('No mensa found for id '.$id);

        // respond
        $response = new JsonResponse();
        $response->setData($mensa->toArray());
        return $response;
    }

    /**
     * Get all the mensas with their details by GET request.
     *
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getAllAction()
    {
        // retrieve mensas
        $mensas = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Mensa')
            ->findAll();

        $mensasArray = array();
        foreach ($mensas as $mensa)
            $mensasArray[] = $mensa->toArray();

        // respond
        $response = new JsonResponse();
        $response->setData($mensasArray);
        return $response;
    }

    /**
     * Get the daily menu of a specific date of a mensa by GET request.
     * 
     * @param integer $id mensa id
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMenusFromDateAction($id, $year, $month, $day)
    {
        $em = $this->getDoctrine()
            ->getManager();
        
        // creating doctrines result set mapping obj.
        $rsm = new ResultSetMapping();
        
        // mapping results to the message entity
        $rsm->addEntityResult('MensaBattleAPIBundle:DailyMenu', 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'date', 'date');
        
        $sql = 'SELECT d.id, d.date
            FROM mensa_dailymenu md,
                dailymenu d
            WHERE md.dailymenu_id = d.id
                AND md.mensa_id = ?
                AND YEAR(d.date) = ?
                AND MONTH(d.date) = ?
                AND DAY(d.date) = ?';
        
        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameters(array($id, $year, $month, $day));
        $dailyMenus = $query->getResult();
        
        $dailyMenuArray = array();
        if($dailyMenus)
            foreach ($dailyMenus as $dailyMenu)
                $dailyMenuArray[] = $dailyMenu->toArray();
      
        // respond
        $response = new JsonResponse();
        $response->setData($dailyMenuArray);
        return $response;
    }

    /**
     * Get the next daily menus of a mensa by GET request.
     * 
     * @param integer $id mensa id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getNextMenusAction($id)
    {
        // retrieve mensa
        $mensa = $this->getDoctrine()
           ->getRepository('MensaBattleAPIBundle:Mensa')
           ->find($id);
        if(!$mensa)
           throw $this->createNotFoundException('No mensa found for id '.$id);
      
        $dailyMenuArray = array();
        foreach ($mensa->getDailyMenus() as $dailyMenu)
            if ($dailyMenu->getDate() >= new \DateTime())
                $dailyMenuArray[] = $dailyMenu->toArray();
      
        // respond
        $response = new JsonResponse();
        $response->setData($dailyMenuArray);
        return $response;
    }

    /**
     * Get the past daily menus of a mensa by GET request.
     * 
     * @param integer $id mensa id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getPastMenusAction($id)
    {
        // retrieve mensa
        $mensa = $this->getDoctrine()
           ->getRepository('MensaBattleAPIBundle:Mensa')
           ->find($id);
        if(!$mensa)
           throw $this->createNotFoundException('No mensa found for id '.$id);
      
        $dailyMenuArray = array();
        foreach ($mensa->getDailyMenus() as $dailyMenu)
            if ($dailyMenu->getDate() < new \DateTime())
                $dailyMenuArray[] = $dailyMenu->toArray();
      
        // respond
        $response = new JsonResponse();
        $response->setData($dailyMenuArray);
        return $response;
    }

    /**
     * Get all the daily menus of a mensa by GET request.
     * 
     * @param integer $id mensa id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getMenusAction($id)
    {
        // retrieve mensa
        $mensa = $this->getDoctrine()
           ->getRepository('MensaBattleAPIBundle:Mensa')
           ->find($id);
        if(!$mensa)
           throw $this->createNotFoundException('No mensa found for id '.$id);
      
        $dailyMenuArray = array();
        foreach ($mensa->getDailyMenus() as $dailyMenu)
            $dailyMenuArray[] = $dailyMenu->toArray();
      
        // respond
        $response = new JsonResponse();
        $response->setData($dailyMenuArray);
        return $response;
    }
}