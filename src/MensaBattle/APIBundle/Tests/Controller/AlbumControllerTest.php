<?php

use Symfony\Component\BrowserKit\Cookie;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlbumControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        
        // set cookie
        $id = rand(1, 10000000);
        $cookie = new Cookie('fbtoken', md5($id));
        $client->getCookieJar()->set($cookie);
        
        // create parameters
        $parameters = array('id' => $id,
            'name' => 'Max Mustermann',
            'link' => 'https://www.facebook.com/max');
        
        $crawler = $client->request('POST', '/person', $parameters);
        
        print_r($crawler->filter('div.text_exception h1')->text());
        
        
        $crawler = $client->request('GET', '/person/'.$id);
        
        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        
        $response = json_decode($client->getResponse()->getContent());
        $this->assertInternalType('array', $response);
    }
}
