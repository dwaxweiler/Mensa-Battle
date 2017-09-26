<?php

namespace MensaBattle\APIBundle\Controller;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AlbumController extends Controller
{
    /**
     * Create a photo.
     *
     * @throws \Exception
     */
    public function createPhotoAction()
    {
        $params = array();
        $content = $this->getRequest()->getContent();
        if (!empty($content))
            $params = json_decode($content, true);

        // check if parameters are set
        if (empty($params['fbtoken']))
            throw new \Exception('No facebook token.');
        if (empty($params['photo']))
            throw new \Exception('No photo.');

        // retrieve person
        $personId = $this->get('facebook_connector')->getUserId($params['fbtoken']);
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($personId);
        if (!$person)
            throw $this->createNotFoundException('No person found for id '.$personId);

        // create a photo
        $photo = base64_decode($params['photo']);
        $relativePhotoPath = '/mensabattle/web/photos/'.md5(time()).'.jpg';
        $photoPath = $_SERVER['DOCUMENT_ROOT'].$relativePhotoPath;
        $fp = fopen($photoPath, 'xb');
        fwrite($fp, $photo);
        fclose($fp);

        $image = $person->getAlbum()->createPhoto($this->getDoctrine()->getEntityManager(), $relativePhotoPath);
        $response = new JsonResponse();
        $response->setData($image->toArray());
        return $response;
    }

    /**
     * Get a photo.
     *
     * @param integer $id photo id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPhotoAction($id)
    {
        // retrieve photo
        $photo = $this->getDoctrine()->getRepository('MensaBattleAPIBundle:Photo')->find($id);
        if (!$photo)
            throw $this->createNotFoundException('No photo found for id '.$id);
        
        // respond
        $path = $photo->getFilePath();
        $file = fopen($_SERVER['DOCUMENT_ROOT'].$path, 'rb');
        $stream = stream_get_contents($file);
        return new Response($stream, 200, array('Content-Type' => 'image/jpg'));
    }
    
    /**
     * Delete a photo.
     * 
     * @param integer $id photo id
     * @throws \Exception
     */
    public function deletePhotoAction($id)
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
        $photo = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Photo')
            ->find($id);
        if (!$photo)
            throw $this->createNotFoundException('No photo found for id '.$id);
        
        $person->getAlbum()->deletePhoto($this->getDoctrine()->getEntityManager(), $photo);
    }
}
