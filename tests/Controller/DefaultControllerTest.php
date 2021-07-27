<?php


namespace App\Tests\Controller;


use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    use NeedLogin;

    protected function getEntity($user)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $user]);
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
        $client = static::createClient();
        $user = $this->getEntity('user');

        $this->login($client, $user);
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('title', 'To Do List app');
        $this->assertSelectorNotExists('button', 'Créer un utilisateur');
    }

    public function testIndexWithAdminCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');

        $this->login($client, $user);
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('.btn.btn-info', 'Liste des utilisateurs');
    }
}