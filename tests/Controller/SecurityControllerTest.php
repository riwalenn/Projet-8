<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use NeedLogin;

    protected function getEntity($user)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $user]);
    }

    public function testLoginURI()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            '_username' => 'fakeUsername',
            '_password' => 'fakePassword'
        ]);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithBadToken()
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            '_csrf_token' => 'token_test',
            '_username' => 'user',
            '_password' => 'user'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfulLogin()
    {
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            '_username' => 'user',
            '_password' => 'user'
        ]);
        $user = $this->getEntity('user');
        $this->login($client, $user);
        $this->assertResponseRedirects('/');
        $client->followRedirect();
    }

    public function testForbiddenAccessUser()
    {
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            '_username' => 'user',
            '_password' => 'user'
        ]);
        $user = $this->getEntity('user');

        $this->login($client, $user);

        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testLogout()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');

        $this->login($client, $user);
        $client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}