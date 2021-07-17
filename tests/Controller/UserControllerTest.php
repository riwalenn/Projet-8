<?php


namespace App\Tests\Controller;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateUser()
    {

    }

    public function testEditUser()
    {

    }


}