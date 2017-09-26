<?php

namespace MensaBattle\FacebookAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MensaBattle\APIBundle\Entity\Person;

class HighscoresController extends Controller
{
    /**
     * Return the highscore list detetmined by the type.
     * 
     * @param string $type
     */
    public function indexAction($type)
    {
        if ($type != 'week' && $type != 'month' && $type != 'total')
            throw $this->createNotFoundException('This highscore list type does not exist.');
        
        // retrieve persons
        $persons = $this->getDoctrine()
            ->getRepository('MensaBattleAPIBundle:Person')
            ->findAll();
        
        // sort persons
        $output = array();
        if ($persons)
        {
            if ($type == 'week')
                foreach ($persons as $person)
                    $output[] = array(
                        'id' => $person->getId(),
                        'name' => $person->getName(),
                        'score' => $person->calculateLastWeekScore($this->getDoctrine()->getManager())
                    );
            elseif ($type == 'month')
                foreach ($persons as $person)
                    $output[] = array(
                        'id' => $person->getId(),
                        'name' => $person->getName(),
                        'score' => $person->calculateLastMonthScore($this->getDoctrine()->getManager())
                    );
            else
                foreach ($persons as $person)
                    $output[] = array(
                        'id' => $person->getId(),
                        'name' => $person->getName(),
                        'score' => $person->calculateTotalScore($this->getDoctrine()->getManager())
                    );
            usort($output, array($this, 'compareScores'));
        }
        
        return $this->render(
            'MensaBattleFacebookAppBundle:Highscores:index.html.twig',
            array('persons' => $output)
        );
    }
    
    /**
     * Compare function for two associative arrays with the type 'score'.
     * 
     * @param unknown $person1 first array
     * @param unknown $person2 second array
     * @return -1, 0, 1 when first score is smaller, equal to, greater than second one
     */
    private function compareScores($person1, $person2)
    {
        if ($person1['score'] == $person2['score'])
            return 0;
        elseif ($person1['score'] > $person2['score'])
            return -1;
        else
            return 1;
    }
}