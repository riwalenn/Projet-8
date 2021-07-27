<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use NeedLogin;

    const URIS = [
        'list'          => '/users',
        'editUser'      => '/users/1/edit',
        'createUser'    => '/users/create',
    ];

    protected function getEntity($username)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    /**
     * @param $uri
     */
    protected function loginWithoutCredentials($uri)
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
    protected function loginWithCredentials($role, $uri, int $http_response = Response::HTTP_OK)
    {
        $client = static::createClient();
        $user = $this->getEntity($role);
        $this->login($client, $user);

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame($http_response);
    }

    protected function newEntity(): User
    {
        return (new User())
            ->setUsername('test')
            ->setEmail('test@gmail.com')
            ->setPassword('FM<gbO!SI)FD?ASy5"')
            ->setRoles(array('ROLE_USER'));
    }

    public function testUrisList()
    {
        $uris = self::URIS;
        $this->loginWithoutCredentials($uris["list"]);
        $this->loginWithCredentials('user', $uris["list"], Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
        $this->loginWithCredentials('admin', $uris["list"]);
        $this->assertSelectorExists('h1');
    }

    public function testUrisCreate()
    {
        $uris = self::URIS;
        $this->loginWithoutCredentials($uris["createUser"]);
        $this->loginWithCredentials('user', $uris["createUser"], Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
        $this->loginWithCredentials('admin', $uris["createUser"]);
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

    public function testUrisEdit()
    {
        $uri = '/users/7/edit';
        $this->loginWithoutCredentials($uri);
        $this->loginWithCredentials('user', $uri, Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
        $this->loginWithCredentials('admin', $uri);
        $this->assertSelectorExists('h1');
    }
}