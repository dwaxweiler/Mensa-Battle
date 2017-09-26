<?php

namespace MensaBattle\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MealController extends Controller
{
    /**
     * Get the details of a meal by GET request.
     *
     * @param integer $id meal id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getAction($id)
    {
        // retrieve meal
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($id);
        if(!$menu)
            throw $this->createNotFoundException('No meal found for id '.$id);

        // respond
        $response = new JsonResponse();
        $response->setData($menu->toArray());
        return $response;
    }

    /**
     * Get the rating of a meal by GET request.
     *
     * @param integer $rid rating id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getRatingAction($rid)
    {
        // retrieve rating
        $rating = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Rating')
            ->find($rid);
        if(!$rating)
            throw $this->createNotFoundException('No rating found for id '.$rid);

        // respond
        $response = new JsonResponse();
        $response->setData($rating->toArray());
        return $response;
    }

    /**
     * Get the ratings of a meal by GET request.
     *
     * @param integer $id meal id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getRatingsAction($id)
    {
        // retrieve meal
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($id);
        if(!$menu)
            throw $this->createNotFoundException('No meal found for id '.$id);

        $ratingsArray = array();
        foreach ($menu->getMeal()->getRatings() as $rating)
            $ratingsArray[] = $rating->toArray();

        // respond
        $response = new JsonResponse();
        $response->setData($ratingsArray);
        return $response;
    }

    /**
     * Get a example of a meal by GET request.
     *
     * @param integer $eid example id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getExampleAction($eid)
    {
        // retrieve example
        $example = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Example')
            ->find($eid);
        if (!$example)
            throw $this->createNotFoundException('No example found for id '.$eid);

        // respond
        $response = new JsonResponse();
        $response->setData($example->toArray());
        return $response;
    }

    /**
     * Get the examples of a meal by GET request.
     *
     * @param integer $id meal id
     * @return \MensaBattle\APIBundle\Controller\JsonResponse
     */
    public function getExamplesAction($id)
    {
        // retrieve meal
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($id);
        if(!$menu)
            throw $this->createNotFoundException('No meal found for id '.$id);

        // get examples
        $examplesArray = array();
        foreach ($menu->getMeal()->getExamples() as $example)
            $examplesArray[] = $example->toArray();

        // respond
        $response = new JsonResponse();
        $response->setData($examplesArray);
        return $response;
    }

     /**
      * Creates a rating of a meal using POST variables.
      *
      * @param integer $id meal id
      * @throws \Exception
      */
    public function createRatingAction($id)
    {
        $params = array();
        $content = $this->getRequest()->getContent();
        if (!empty($content))
            $params = json_decode($content, true);

        // check if parameters are set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
        if (empty($params['score']))
            throw new \Exception('No score.');

        // retrieve objects
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($id);
        if (!$menu)
            throw $this->createNotFoundException('No meal found for id '.$id);

        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);

        $rating = $menu->getMeal()->rate($this->getDoctrine()->getEntityManager(), $person, $params['score']);

        // respond
        $response = new JsonResponse();
        $response->setData($rating->toArray());
        return $response;
    }

    /**
     * Creates an example of a meal using POST variables.
     *
     * @param integer $id meal id
     * @throws \Exception
     */
    public function createExampleAction($id)
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
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($id);
        if (!$menu)
            throw $this->createNotFoundException('No meal found for id '.$id);

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

        $example = $menu->getMeal()->createExample($this->getDoctrine()->getEntityManager(), $person, $photo);

        // respond
        $response = new JsonResponse();
        $response->setData($example->toArray());
        return $response;
    }

    /**
     * Delete an example of a meal using DELETE variables.
     *
     * @param integer $mid meal id
     * @param integer $eid example id
     * @throws \Exception
     */
    public function deleteExampleAction($mid, $eid)
    {
        // check if fbtoken is set
        if (!$request->cookies->has('fbtoken'))
          throw new \Exception('No facebook token.');
        
        // retrieve objects
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($mid);
        if (!$menu)
           throw $this->createNotFoundException('No meal found for id '.$mid);
        
        $example = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Example')
            ->find($eid);
        if (!$example)
           throw $this->createNotFoundException('No example found for id '.$eid);
        
        $personId = $this->get('facebook_connector')
            ->getUserId($request->cookies->get('fbtoken'));
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);
        
        $menu->getMeal()->deleteExample($this->getDoctrine()->getEntityManager(), $example, $person);
    }
}
