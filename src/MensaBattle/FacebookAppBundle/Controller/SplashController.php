<?php

namespace MensaBattle\FacebookAppBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SplashController extends Controller
{
    public function showAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        }
        else
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Splash:index.html.twig',
            array(
                'error' => $error
            )
        );
    }
    
    public function checkAction()
    {
        // Call intercepted by the Security Component of Symfony
    }
    
    public function logoutAction()
    {
        // Call intercepted by the Security Component of Symfony
    }
}
