<?php


namespace App\Tests\Controller;


use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    use NeedLogin;

    protected function getEntity($username)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    /**
     * @param $role
     * @param $uri
     * @param int $http_response
     */
    protected function LoginWithCredentials($role, $uri, int $http_response = Response::HTTP_OK)
    {
        $client = static::createClient();
        $user = $this->getEntity($role);
        $this->login($client, $user);

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame($http_response);
    }

    public function testIndexWithoutCredentials()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('title', 'To Do List app');
        $this->assertSelectorNotExists('.btn.btn-info', 'Consulter la liste des tâches à faire');
    }

    public function testIndexWithUserCredentials()
    {
        $this->LoginWithCredentials('user', '/');

        $this->assertSelectorTextContains('title', 'To Do List app');
        $this->assertSelectorNotExists('button', 'Créer un utilisateur');
    }

    public function testIndexWithAdminCredentials()
    {
        $this->LoginWithCredentials('admin', '/');

        $this->assertSelectorTextContains('.btn.btn-info', 'Liste des utilisateurs');
    }
}