<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use NeedLogin;

    const URIS = [
        'list'          => '/tasks',
        'listDone'      => '/tasks/done',
        'editTask'      => '/tasks/1/edit',
        'createTask'    => '/tasks/create',
        'toggleTask'    => '/tasks/1/toggle',
        'deleteTask'    => '/tasks/1/delete'
    ];

    /**
     * @param $username
     * @return mixed
     */
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
        $uris = self::URIS;
        $this->testWithoutCredentials($uris["list"]);
        $this->testWithCredentials('user', $uris["list"]);
        $this->testWithCredentials('admin', $uris["list"]);
    }

    public function testUriIsDoneList()
    {
        $uris = self::URIS;
        $this->testWithoutCredentials($uris["listDone"]);
        $this->testWithCredentials('user', $uris["listDone"]);
        $this->testWithCredentials('admin', $uris["listDone"]);
    }

    public function testUriCreate()
    {
        $uris = self::URIS;
        $this->testWithoutCredentials($uris["createTask"]);
        $this->testWithCredentials('user', $uris["createTask"]);
        $this->testWithCredentials('admin', $uris["createTask"]);
    }

    public function testUriEdit()
    {
        $uris = self::URIS;
        $this->testWithoutCredentials($uris["editTask"]);
        $this->testWithCredentials('user', $uris["editTask"]);
        $this->testWithCredentials('admin', $uris["editTask"]);
    }

    public function testUriToggle()
    {
        $uris = self::URIS;
        $this->testWithoutCredentials($uris["toggleTask"]);
        $this->testWithCredentials('user', $uris["toggleTask"], Response::HTTP_FOUND);
        $this->testWithCredentials('admin', $uris["toggleTask"], Response::HTTP_FOUND);
    }

    public function testDeleteWithoutCredential()
    {
        $uris = self::URIS;
        $this->testWithoutCredentials($uris["deleteTask"]);
    }

    /* ATTENTION : suppression en bdd */
    public function testDeleteWithCredential()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');
        $this->login($client, $user);

        $client->request('GET', '/tasks/23/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testDeleteAnonymousWithoutCredential()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');
        $this->login($client, $user);

        $client->request('GET', '/tasks/1/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks/done');
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testDeleteAnonymousWithCredential()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');
        $this->login($client, $user);

        $client->request('GET', '/tasks/1/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}