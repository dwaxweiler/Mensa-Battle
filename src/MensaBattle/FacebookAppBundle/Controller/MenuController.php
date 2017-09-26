<?php

namespace MensaBattle\FacebookAppBundle\Controller;

use MensaBattle\FacebookAppBundle\Form\Type\RatingType;
use MensaBattle\APIBundle\Entity\Rating;
use Doctrine\ORM\NoResultException;
use MensaBattle\APIBundle\Entity\DailyMenu;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller
{
    public function indexAction()
    {
        return $this->forward('MensaBattleFacebookAppBundle:Menu:byDate', array(
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d')
        ));
    }
    
    public function byDateAction($year = null, $month = null, $day = null)
    {
        // set mensa
        $mensa = 1;
        
        // set dates
        $date = new \DateTime($year.'-'.$month.'-'.$day);
        $previousDate = clone $date;
        $previousDate->sub(new \DateInterval('P1D'));
        $nextDate = clone $date;
        $nextDate->add(new \DateInterval('P1D'));
        
        // creating doctrines result set mapping object
        $rsm = new ResultSetMapping();
        
        // mapping results to the message entity
        $rsm->addEntityResult('MensaBattleAPIBundle:DailyMenu', 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'date', 'date');
        
        // query
        $sql = 'SELECT d.id, d.date
              FROM mensa_dailymenu md,
                  dailymenu d
              WHERE md.dailymenu_id = d.id
                  AND md.mensa_id = ?
                  AND YEAR(d.date) = ?
                  AND MONTH(d.date) = ?
                  AND DAY(d.date) = ?';
        $query = $this->getDoctrine()
            ->getManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameters(array($mensa, $date->format('Y'), $date->format('m'), $date->format('d')));
        
        try
        {
            $dailyMenu = $query->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $dailyMenu = false;
        }
        
        $menus = false;
        if ($dailyMenu)
            $menus = $dailyMenu->getMenus();
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Menu:index.html.twig',
            array(
                'date' => $date,
                'previousDate' => $previousDate,
                'nextDate' => $nextDate,
                'menus' => $menus
                )
        );
    }
    
    public function showAction($id)
    {
        // retrieve menu
        $menu = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Menu')
            ->find($id);
        if (!$menu)
            return $this->render('MensaBattleFacebookAppBundle:Menu:menunotfound.html.twig');
        
        $rating = new Rating();
        
        $form = $this->createForm(new RatingType(), $rating);
        
        $request = $this->getRequest();
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
        
            if ($form->isValid())
            {
                $person = $this->getDoctrine()->getRepository('MensaBattleAPIBundle:Person')->find($this->getUser()->getId());
                
                // check if a rating from this user for this meal already exists
                foreach ($menu->getMeal()->getRatings() as $rating)
                    if ($rating->getAuthor()->getId() == $person->getId())
                        return $this->redirect($this->generateUrl('facebook_app_menu', array(
                            'id' => $id
                        )));
                
                $meal = $menu->getMeal();
                $rating->setMeal($meal);
                $meal->addRating($rating);
                $rating->setAuthor($person);
            
                // save to database
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($rating);
                $em->flush();
            
                return $this->redirect($this->generateUrl('facebook_app_menu', array(
                    'id' => $id
                )));
            }
        }
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Menu:menu.html.twig',
            array('menu' => $menu,
                'form' => $form->createView()
        ));
    }
}
