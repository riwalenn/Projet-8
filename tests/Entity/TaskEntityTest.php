<?php


namespace App\Tests\Entity;


use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TaskEntityTest extends KernelTestCase
{
    public function getEntity(): Task
    {
        return (new Task())
            ->setUser(new User())
            ->setContent("Lorem Ipsum is simply dummy text of the printing and typesetting industry.")
            ->setCreatedAt(new \DateTime("2021-06-30 18:00:00"))
            ->setTitle("Hello i'm here !")
            ->setIsDone(true);
    }

    public function assertHasErrors(Task $task, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($task);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    /* tests */

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(Task::class, $this->getEntity());
    }

    public function testHasAttributesEntity()
    {
        $this->assertObjectHasAttribute('content', $this->getEntity());
        $this->assertObjectHasAttribute('createdAt', $this->getEntity());
        $this->assertObjectHasAttribute('title', $this->getEntity());
        $this->assertObjectHasAttribute('isDone', $this->getEntity());
    }

    public function testTypeStringTitle()
    {
        $this->assertIsString($this->getEntity()->getTitle());
    }

    public function testTypeStringContent()
    {
        $this->assertIsString($this->getEntity()->getContent());
    }

    public function testInvalidBlankContent()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
    }

    public function testInvalidBlankTitle()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 1);
    }

    public function testIDoneCanNotbeBlank()
    {
        $this->assertHasErrors($this->getEntity()->setIsDone(''), 0);
    }
}