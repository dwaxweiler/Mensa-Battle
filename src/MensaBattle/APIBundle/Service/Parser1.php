<?php

namespace MensaBattle\APIBundle\Service;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Doctrine\ORM\Query\ResultSetMapping;

use MensaBattle\APIBundle\Entity\Menu;

use Symfony\Bridge\Monolog\Logger;

use Doctrine\ORM\EntityManager;

use MensaBattle\APIBundle\Entity\Mensa;

use MensaBattle\APIBundle\Entity\Meal;

use MensaBattle\APIBundle\Entity\DailyMenu;

/**
 * Parser for the mensa of Saarland University.
 */
class Parser1 implements Parser
{
    /**
     * URL to the xml file
     * 
     * @var string
     */
  	private $url = "http://studentenwerk-saarland.de/_menu/actual/speiseplan-saarbruecken.xml";
  	
  	/**
  	 * Entity manager
  	 * 
  	 * @var EntityManager
  	 */
  	private $em;
  	
  	/**
  	 * Logger
  	 * 
  	 * @var Logger
  	 */
  	private $logger;
    
  	public function __construct(EntityManager $em, Logger $logger)
  	{
  	    $this->em = $em;
  	    $this->logger = $logger;
  	}
  	
  	public function parse()
  	{
    		// get entire XML File
    		$speiseplan = simpleXML_load_file($this->url, "SimpleXMLElement",	LIBXML_NOCDATA);
        
    		// get mensa
    		$mensa = $this->em
    		    ->getRepository('MensaBattleAPIBundle:Mensa')
    		    ->find('1');    		
    		
    		// create for each day ("Tag") a new daily menu, including all meals
    		foreach ($speiseplan->tag as $tag)
    		{
      			$date = \DateTime::createFromFormat('U', $tag['timestamp']);
      			$date->setTimezone(new \DateTimeZone('Europe/Berlin'));
            
      			// Is there already a daily menu for this date?
      			$dailyMenu = $this->em
      			    ->getRepository('MensaBattleAPIBundle:DailyMenu')
      			    ->findOneByDate($date);
      			if (is_null($dailyMenu))
      			{
          			// create DailyMenu
          			$dailyMenu = new DailyMenu();
          			$dailyMenu->setDate($date);
          			$this->em->persist($dailyMenu);
          			$this->em->flush($dailyMenu);
          			
          			// add daily menu to mensa
          			$mensa->addDailyMenu($dailyMenu);
      			}
      			
      			foreach ($tag->item as $item)
      			{
      			    // assign values
          			$category = html_entity_decode($item->category);
          			$title = html_entity_decode($item->title);
          			$description = html_entity_decode($item->description);
          			$markings = $item->kennzeichnungen;
          			$sideDishes = html_entity_decode($item->beilagen);
          			if ($sideDishes != '')
          			{
          			    // add side dishes to description if it is ever set
          			    $description .= ', '.$sideDishes;
          			    $this->logger->info('The tag <beilagen> contained data!');
          			}
          			$priceStudent = $this->c2p($item->preis1);
          			$priceStaff = $this->c2p($item->preis2);
          			$priceVisitor = $this->c2p($item->preis3);
          			
          			// Is this menu of the daily menu already in the database?
          			$query = $this->em
          			    ->createQuery('SELECT m
                        FROM MensaBattleAPIBundle:Menu m
                        JOIN m.dailyMenus dm
                        WHERE dm.id = :id
                        AND m.name = :name')
          			    ->setParameters(array('name' => $category,
          			        'id' => $dailyMenu->getId()));
          			try
          			{
          			    $menu = $query->getSingleResult();
          			}
          			catch (\Doctrine\Orm\NoResultException $e)
          			{
          			    $menu = null;
          			}
          			if (is_null($menu))
          			{
          			    // create menu
          			    $menu = new Menu();
          			    $menu->setName($category);
          			    $menu->setPriceStudent($priceStudent)
          			        ->setPriceStaff($priceStaff)
          			        ->setPriceVistor($priceVisitor);
          			    $this->em->persist($menu);

          			    // add menu to daily menu
          			    $dailyMenu->addMenu($menu);
          			}
          			else
          			{
          			    // update prices only
            			  $menu->setPriceStudent($priceStudent)
            			      ->setPriceStaff($priceStaff)
            			      ->setPriceVistor($priceVisitor);
          			}
          			$this->em->flush($menu);
      			    
                $meal = $menu->getMeal();
                // check if the title of the meal is still the same
                if (!is_null($meal))
                    if ($meal->getTitle() != $title)
                        $meal = null;
                if (is_null($meal))
                {
                    // Is this meal already in the database?
                    $meal = $this->em
                        ->getRepository('MensaBattleAPIBundle:Meal')
                        ->findOneBy(array('title' => $title,
                            'description' => $description));
                    if (is_null($meal))
                    {
                        // create meal
                        $meal = new Meal();
                        $meal->setTitle($title);
                        $meal->setDescription($description);
                        $this->em->persist($meal);
                    }
                    
                    // link meal to menu
                    $menu->setMeal($meal);
                    $this->em->flush($menu);
                }
                elseif ($meal->getDescription() == '' && !empty($description))
                {
                    // Is the meal with the same title and not empty description already in the database?
                    // scenario: first time parsed: only title is set
                    //           second time parsed: this time! => description would be only updated, but what, if there is already such a meal?
                    $temp = $this->em
                        ->getRepository('MensaBattleAPIBundle:Meal')
                        ->findOneBy(array('title' => $title,
                            'description' => $description));
                    if (is_null($temp))
                    {
                        // update description (no meal with the same attributes exists)
                        $meal->setDescription($description);
                    }
                    else
                    {
                        // remove meal (where only title is set, because there is already a meal where both attributes are set)
                        $menu->setMeal(null);
                        $this->em->flush($menu);
                        $this->em->remove($meal);
                        $this->logger->info('The meal with id "'.$meal->getId().'", title "'.$meal->getTitle().'" and description "'.$meal->getDescription().'" should be deleted.');
                        // set to actual meal
                        $meal = $temp;
                        $menu->setMeal($meal);
                        $this->em->flush($menu);
                    }
                }
                // set/update markings
                $this->updateMarkings($meal, $markings);
                //$this->em->flush($meal);
                
                // commit changes to database
        				$this->em->flush();
      			}
    		}
    		
      	// commit all remaining changes to database
      	$this->em->flush();
  	}
    
  	/**
  	 * Update markings of a meal.
  	 * 
  	 * @param Meal $meal the meal
  	 * @param int[] $markings array of marking numbers
  	 */
  	private function updateMarkings(Meal $meal, $markings)
  	{
    	  $meal->resetMarkings();
    	  
    	  if ($markings == '')
    	      return;
    	  $markingNumbers = explode(",", $markings);
    	  foreach ($markingNumbers as $number)
    	  {
    	      $marking = $this->em
    	          ->getRepository('MensaBattleAPIBundle:Marking')
    	          ->find($number);
    	      if (is_null($marking))
    	          $this->logger->info('There is a new marking number: '.$number);
    	      else
    	          $meal->addMarking($marking);
    	  }
  	}
  	
  	/**
  	 * Replaces commas with points.
  	 * 
  	 * @param string $float
  	 * @return float
  	 */
  	private function c2p($float)
  	{
  	    return floatval(str_replace(',', '.', $float));
  	}
}