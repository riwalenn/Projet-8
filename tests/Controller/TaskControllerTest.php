<?php


namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\NeedLogin;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use NeedLogin;

    const URIS = [
        'list'          => '/tasks',
        'listDone'      => '/tasks/done',
        'editTask'      => '/tasks/1/edit',
        'createTask'    => '/tasks/create',
        'toggleTask'    => '/tasks/15/toggle',
        'deleteTask'    => '/tasks/10/delete'
    ];

    /**
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * @param $username
     * @return mixed
     */
    protected function getEntity($username)
    {
        return self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    protected function getTasksAnonymousEntity()
    {
        return self::$container->get('doctrine')->getManager()->getRepository(Task::class)->findBy(['user' => $this->getEntity('anonyme')]);
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

    public function testDeleteWithCredential()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');
        $this->login($client, $user);

        $client->request('GET', '/tasks/15/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testDeleteAnonymousWithoutCredential()
    {
        $client = static::createClient();
        $user = $this->getEntity('user');
        $tasks = $this->getTasksAnonymousEntity();
        $this->login($client, $user);

        foreach ($tasks as $task) {
            $client->request('GET', '/tasks/' . $task->getId() . '/delete');

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
            $this->assertResponseRedirects('/tasks/done');
            $client->followRedirect();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertSelectorExists('.alert.alert-danger');
        }
    }

    public function testDeleteAnonymousWithCredential()
    {
        $client = static::createClient();
        $tasks = $this->getTasksAnonymousEntity();
        $user = $this->getEntity('admin');
        $this->login($client, $user);
        foreach ($tasks as $task) {
            $client->request('GET', '/tasks/' . $task->getId() . '/delete');
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        }
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $this->setCommand('doctrine:database:drop --force');

        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}