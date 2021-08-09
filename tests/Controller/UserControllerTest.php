<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use NeedLogin;

    const URIS = [
        'list'          => '/users',
        'createUser'    => '/users/create',
    ];

    /**
     * @var ObjectManager
     */
    protected $entityManager;

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
     * @param $username
     * @param $uri
     * @param int $http_response
     */
    protected function loginWithCredentials($username, $uri, int $http_response = Response::HTTP_OK)
    {
        $client = static::createClient();
        $user = $this->getEntity($username);
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
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->setCommand('app:link-anonymous ');
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
        $user = $this->getEntity('user');
        $uri = '/users/' . $user->getId() . '/edit';
        $this->loginWithoutCredentials($uri);
        $this->loginWithCredentials('user', $uri, Response::HTTP_FORBIDDEN);
        $this->assertSelectorExists('h3');
        $this->loginWithCredentials('admin', $uri);
        $this->assertSelectorExists('h1');
    }
}