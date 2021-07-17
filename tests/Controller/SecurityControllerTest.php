<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
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

    public function testSuccessfulLogin()
    {
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            '_username' => 'test',
            '_password' => 'test'
        ]);
        $this->assertResponseRedirects('/');
        $client->followRedirect();
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}