<?php

namespace MensaBattle\FacebookAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function showAction()
    {
        return $this->forward(
            'MensaBattleFacebookAppBundle:Profile:byId',
            array('id' => ($this->get('security.context')->isGranted('ROLE_USER') ? $this->getUser()->getId() : '-1'))
        );
    }
    
    public function byIdAction($id)
    {
        // retrieve person
        $person = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->find($id);
        if (!$person)
            return $this->render('MensaBattleFacebookAppBundle:Profile:personnotfound.html.twig');
        
        $score = $person->calculateTotalScore($this->getDoctrine()->getManager());
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Profile:person.html.twig',
            array(
                'person' => $person,
                'score' => $score    
            )
        );
    }
    
}
