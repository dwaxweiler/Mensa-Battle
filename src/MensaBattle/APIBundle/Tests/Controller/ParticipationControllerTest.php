<?php

namespace MensaBattle\APIBundle\Tests\Controller;

use MensaBattle\APIBundle\Entity\Photo;

use MensaBattle\APIBundle\Entity\Person;

use MensaBattle\APIBundle\Entity\Battle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipationControllerTest extends WebTestCase {

	public function testIndex(){
// 		$client = static::createClient();
		
// 		$battleT = new Battle();
// 		$bId = $battleT->getId();
// 		$bId->assertNotNull;
		
// 		$personT = new Person();
// 		$pId = $personT->getId();
// 		$pId->assertNotNull;
		
// 		$photoT = new Photo();
// 		$fId = $photoT->getId();
// 		$fId->assertNotNull;

// 		$client->request('POST', '\participation',
// 				array(
// 						'battleId' => $bId,
// 						'personId' => $pId,
// 						'photoId' => $fId
// 				)
// 		);

// 		$response = $client->getResponse();

// 		$this->assertNotNull($response->getContent());
// 		$this->assertNotEmpty($response->getContent());

// 		$data = json_decode($response->getContent(), true);

// 		$this->assertArrayHasKey('participationId', $data);
// 		$this->assertNotEmpty($data['participationId']);
// 		$this->assertInternalType('integer', $data['participationId']);
	}
}