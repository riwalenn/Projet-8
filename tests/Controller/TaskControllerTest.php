<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testIsDoneList()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/done');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /*public function testCreateTask()
    {

    }

    public function testEditTask()
    {

    }

    public function testToggleTask()
    {

    }

    public function testDeleteTask()
    {

    }*/
}