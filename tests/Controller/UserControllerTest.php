<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use NeedLogin;

    protected function getEntity($username)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    protected function newEntity(): User
    {
        return (new User())
            ->setUsername('test')
            ->setEmail('test@gmail.com')
            ->setPassword('FM<gbO!SI)FD?ASy5"')
            ->setRoles(array('ROLE_USER'));
    }

    public function testListWhithoutCredentials()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testListHasUserCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');

        $this->login($client, $user);
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
    }

    public function testListHasAdminCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');

        $this->login($client, $user);
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1');
    }

    public function testCreateWhithoutCredentials()
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testCreateHasUserCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');

        $this->login($client, $user);
        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
    }

    public function testCreateHasAdminCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');

        $this->login($client, $user);
        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1');
    }

    public function testCreateBadUser()
    {
        $user = $this->newEntity();
        $user->setUsername('user')
                ->setPassword('test_password')
                ->setEmail('user@gmail.com')
                ->setRoles(array('ROLE_USER'));

        $this->assertFalse($user->getUsername() !== "user", "Cet username existe déjà.");
        $this->assertFalse($user->getEmail() !== "user@gmail.com", "Cet email existe déjà.");
    }

    public function testEditWhithoutCredentials()
    {
        $client = static::createClient();
        $client->request('GET', '/users/' . $this->getEntity('user')->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testEditHasUserCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');

        $this->login($client, $user);
        $client->request('GET', '/users/' . $this->getEntity('user')->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
    }

    public function testEditHasAdminCredentials()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');

        $this->login($client, $user);
        $client->request('GET', '/users/' . $this->getEntity('user')->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1');
    }
}