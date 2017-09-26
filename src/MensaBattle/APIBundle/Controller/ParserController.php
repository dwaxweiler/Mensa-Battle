<?php

namespace MensaBattle\APIBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use MensaBattle\APIBundle\Service\Parser1;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParserController extends Controller
{
    /**
     * Start all parsers.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parseAction()
    {
        $this->get('parser1')->parse();
        
        return new Response('<html><body>Parsers have run.</body></html>');
    }
}
