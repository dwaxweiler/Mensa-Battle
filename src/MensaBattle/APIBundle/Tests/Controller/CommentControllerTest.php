<?php

namespace MensaBattle\APIBundle\Tests\Controller;

use MensaBattle\APIBundle\Entity\Person;

use MensaBattle\APIBundle\Entity\Participation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase {
	
	public function testIndex(){
// 		$client = static::createClient();
		
// 		$participant = new Participation();
// 		$pId = $participant->getId();
// 		$pId->assertNotNull;
		
// 		$author = new Person();
// 		$aId = $author->getId();
// 		$aId->asserNotNull;
		
// 		$client->request('POST', '\comment',
// 				array(
// 						'participationId' => $pId,
// 						'message' => 'das rockt!',
// 						'authorId' => $aId
// 						)
// 				);
		
// 		$response = $client->getResponse();
		
// 		$this->assertNotNull($response->getContent());
// 		$this->assertNotEmpty($response->getContent());
		
// 		$data = json_decode($response->getContent(), true);
		
// 		$this->assertArrayHasKey('commentId', $data);
// 		$this->assertNotEmpty($data['commentId']);
// 		$this->assertInternalType('integer', $data['commentId']);
// 		$this->assertNotEmpty($data['message']);
// 		$this->assertInternalType('string', $data['message']);
	}
}