<?php


namespace App\Tests\Controller;


use App\Entity\User;
use App\Tests\NeedLogin;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    use NeedLogin;

    /**
     * @throws Exception
     */
    protected function setCommand($string): int
    {
        $kernel = static::createKernel(['APP_ENV' => 'test']);
        $application =new Application($kernel);
        $application->setAutoExit(false);
        return $application->run(new StringInput(sprintf('%s --quiet', $string)));
    }

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->setCommand('doctrine:database:drop --force');
        $this->setCommand('doctrine:database:create');
        $this->setCommand('doctrine:schema:create');
        $this->setCommand('doctrine:fixtures:load');
        $this->setCommand('app:link-anonymous ');
    }

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

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $this->setCommand('doctrine:database:drop --force');

        parent::tearDown();
    }
}