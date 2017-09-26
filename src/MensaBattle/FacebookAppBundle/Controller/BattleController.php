<?php

namespace MensaBattle\FacebookAppBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use MensaBattle\APIBundle\Entity\Battle;
use MensaBattle\FacebookAppBundle\Form\Type\BattleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BattleController extends Controller
{
    public function indexAction()
    {
        // retrieve battles
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT b
            FROM MensaBattleAPIBundle:Battle b
            WHERE b.endTime >= CURRENT_TIMESTAMP()
            ORDER BY b.endTime ASC'
        );
        $battles = $query->getResult();
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Battle:index.html.twig',
            array('battles' => $battles)
        );
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction()
    {
        $battle = new Battle();
        
        $form = $this->createForm(new BattleType(), $battle);

        $request = $this->getRequest();
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            
            if ($form->isValid())
            {
                $trophy = $battle->getTrophy();
                $trophy->setTitle($battle->getTitle());
                $trophy->setIconPath('test');
                
                // save to database
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($battle);
                $em->persist($trophy);
                $em->flush();
                
                return $this->redirect($this->generateUrl('facebook_app_battle', array(
                    'id' => $battle->getId()
                )));
            }
        }
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Battle:new.html.twig',
            array('form' => $form->createView()));
    }
    
    public function showAction($id)
    {
        // retrieve battle
        $battle = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Battle')
            ->find($id);
        if (!$battle)
            return $this->render('MensaBattleFacebookAppBundle:Battle:battlenotfound.html.twig');
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Battle:battle.html.twig',
            array('battle' => $battle)
        );
    }
}