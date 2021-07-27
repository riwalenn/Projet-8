<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use NeedLogin;

    protected function getEntity($username)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    /**
     * @param $uri
     */
    protected function testWithoutCredentials($uri)
    {
        $client = static::createClient();
        $client->request('GET', $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    /**
     * @param $role
     * @param $uri
     * @param int $http_response
     */
    protected function testWithCredentials($role, $uri, int $http_response = Response::HTTP_OK)
    {
        $client = static::createClient();
        $user = $this->getEntity($role);
        $this->login($client, $user);

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame($http_response);
    }

    public function testUriList()
    {
        $this->testWithoutCredentials('/tasks');
        $this->testWithCredentials('user','/tasks');
        $this->testWithCredentials('admin','/tasks');
    }

    public function testUriIsDoneList()
    {
        $this->testWithoutCredentials('/tasks/done');
        $this->testWithCredentials('user','/tasks/done');
        $this->testWithCredentials('admin','/tasks/done');
    }

    public function testUriCreate()
    {
        $this->testWithoutCredentials('/tasks/create');
        $this->testWithCredentials('user','/tasks/create');
        $this->testWithCredentials('admin','/tasks/create');
    }

    public function testUriEdit()
    {
        $this->testWithoutCredentials('/tasks/1/edit');
        $this->testWithCredentials('user','/tasks/1/edit');
        $this->testWithCredentials('admin','/tasks/1/edit');
    }

    public function testUriToggle()
    {
        $this->testWithoutCredentials('/tasks/1/toggle');
        $this->testWithCredentials('user','/tasks/1/toggle', Response::HTTP_FOUND);
        $this->testWithCredentials('admin','/tasks/1/toggle', Response::HTTP_FOUND);
    }

    public function testDeleteWithoutCredential()
    {

    }

    public function testDeleteWithCredential()
    {

    }

    public function testDeleteAnonymousWithoutCredential()
    {

    }

    public function testDeleteAnonymousWithCredential()
    {

    }
}