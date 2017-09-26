<?php

namespace MensaBattle\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DailyMenuController extends Controller
{
  	/**
  	 * Get the details of a menu by GET request.
  	 * 
  	 * @param integer $id menu id
  	 * @return \MensaBattle\APIBundle\Controller\JsonResponse
  	 */
  	public function getAction($id)
  	{
    		// retrieve menu
    		$menu = $this->getDoctrine()
    			  ->getRepository('MensaBattleAPIBundle:DailyMenu')
    			  ->find($id);
    		if (!$menu)
    			  throw $this->createNotFoundException('No daily menu found for id '.$id);
    	
    		// respond
    		$response = new JsonResponse();
    		$response->setData($menu->toArray());
    		return $response;
  	}
  	
  	/**
  	 * Get the meals of a menu by GET request.
  	 * 
  	 * @param integer $id menu id
  	 * @return \MensaBattle\APIBundle\Controller\JsonResponse
  	 */
  	public function getMealsAction($id)
  	{
    		// retrieve menu
    		$menu = $this->getDoctrine()
    			  ->getRepository('MensaBattleAPIBundle:DailyMenu')
    			  ->find($id);
    		if (!$menu)
    			throw $this->createNotFoundException('No menu found for id '.$id);
    	
    		$mealsArray = array();
    		foreach ($menu->getMenus() as $meal)
    			  $mealsArray[] = $meal->toArray();
    	
    		// respond
    		$response = new JsonResponse();
    		$response->setData($mealsArray);
    		return $response;
  	}
}