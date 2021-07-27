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

    /* ATTENTION : suppression en bdd -> réinjecter la bdd après tests */
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

    //TODO::associer les tâches nulles à anonyme
    public function testDeleteAnonymousWithCredential()
    {
        $client = static::createClient();
        $user = $this->getEntity('admin');
        $this->login($client, $user);

        $client->request('GET', '/tasks/1/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /*
        POUR TESTS USER

        INSERT INTO `task` (`id`, `user_id`, `created_at`, `title`, `content`, `is_done`) VALUES
        (23, 7, '2021-07-04 06:51:21', 'Consectetur atque nam quam tempora.', 'Impedit blanditiis id deserunt veritatis. At illo asperiores ea ad. Omnis quibusdam ut fugit aut.', 1),
        (24, 7, '2021-06-16 21:36:54', 'Quibusdam aut inventore voluptatum molestias.', 'Quis numquam ipsa dicta optio temporibus eaque dignissimos sunt. Repellat iste necessitatibus magni qui perspiciatis ut consequuntur voluptatem.', 0),
        (25, 7, '2021-07-18 03:15:24', 'Sed odio ipsam rerum sapiente distinctio.', 'Ea eum eos possimus sit dolorem harum magnam. Et ipsa consectetur dolores id dolorem voluptate.', 0),
        (26, 7, '2021-06-06 06:29:57', 'Dolorum eos perferendis unde est.', 'Rerum quis vero amet perspiciatis libero distinctio. Et aut esse consequatur at est ut.', 1),
        (27, 7, '2021-06-19 19:05:39', 'Quod ea rerum consequatur asperiores.', 'Magnam nam sapiente culpa eligendi. Aut iure vel id magnam architecto ex natus nostrum.', 0);

        POUR TESTS ANONYME

        INSERT INTO `task` (`id`, `user_id`, `created_at`, `title`, `content`, `is_done`) VALUES
        (1, NULL, '2021-06-05 18:52:07', 'Tenetur fugit quia voluptate doloremque dolor.', 'Excepturi voluptates doloribus sunt dolor et ut quo. Non cupiditate facilis dolorum autem. Cumque illum reprehenderit optio quo et asperiores enim.', 1),
        (2, NULL, '2021-06-10 09:08:21', 'Excepturi consequuntur distinctio.', 'Omnis dolorem culpa et. Officia omnis dicta fugit laudantium. Minus et atque corrupti officia.', 0),
        (3, NULL, '2021-06-17 11:49:58', 'Libero magnam dolorem nemo corrupti.', 'Voluptatem qui tenetur a reprehenderit nihil explicabo sapiente consequatur. Temporibus tempora illo maiores eum ut.', 0),
        (4, NULL, '2021-07-14 21:59:36', 'Ut aut amet quis ullam.', 'Reiciendis pariatur quia deleniti dolor autem ad voluptatem. Sit dolorem rerum ducimus assumenda voluptate ex incidunt. Tempore soluta ut iste nisi.', 0),
        (5, NULL, '2021-07-12 02:47:14', 'Qui aliquid doloremque cum.', 'Cumque maiores temporibus quos quia. Reprehenderit enim dolor unde aut. Vel ut repellat illum et molestias.', 1);
     */
}