<?php


namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Validator\ConstraintViolation;

class UserEntityTest extends KernelTestCase
{
    private $entityManager;

    /**
     * @throws Exception
     */
    protected function setCommand($string): int
    {
        $kernel = static::createKernel(['APP_ENV' => 'test']);
        $application = new Application($kernel);
        $application->setAutoExit(false);
        return $application->run(new StringInput(sprintf('%s --quiet', $string)));
    }

    protected function setUp(): void
    {
        $this->setCommand('doctrine:database:drop --force');
        $this->setCommand('doctrine:database:create');
        $this->setCommand('doctrine:schema:create');
        $this->setCommand('doctrine:fixtures:load');
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->setCommand('app:link-anonymous ');
    }

    protected function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    protected function getEntity(): User
    {
        return (new User())
            ->setUsername('test')
            ->setEmail('test@gmail.com')
            ->setPassword('FM<gbO!SI)FD?ASy5"')
            ->setRoles(array('ROLE_USER'));
    }

    /* tests */

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(User::class, $this->getEntity());
    }

    public function testHasAttributesEntity()
    {
        $this->assertObjectHasAttribute('username', $this->getEntity());
        $this->assertObjectHasAttribute('email', $this->getEntity());
        $this->assertObjectHasAttribute('password', $this->getEntity());
        $this->assertObjectHasAttribute('roles', $this->getEntity());
    }

    public function testGetid()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->assertEquals($user->getId(), 7);
    }

    public function testInvalidBlankUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
    }

    public function testInvalidBlankEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('@gmail.com'), 1);
    }

    public function testInvalidBlankPassword()
    {
        $this->assertHasErrors($this->getEntity()->setPassword(''), 1);
    }

    public function testTypeArrayRoles()
    {
        $this->assertIsArray($this->getEntity()->getRoles());
    }

    public function testUniqueUsername()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->assertFalse($user->getUsername() !== "user", "Cet username existe d??j??.");
    }

    public function testUniqueEmail()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user@gmail.com']);
        $this->assertFalse($user->getEmail() !== "user@gmail.com", "L'email existe d??j??.");
    }

    public function testAddTask()
    {
        $user = $this->getEntity();
        $task = new Task();
        $task->setUser($user)
            ->setTitle('Id qui illo vitae')
            ->setContent("Dolores necessitatibus sed veniam.")
            ->setIsDone(1)
            ->setCreatedAt(new DateTime());
        $this->assertCount(1, $this->getEntity()->addTask($task)->getTasks());
    }

    public function testRemoveAllUserTasks()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user@gmail.com']);
        $task = new Task();
        $task->setUser($user);
        $this->assertCount(5, $user->removeTask($task)->getTasks());
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