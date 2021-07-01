<?php


namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserEntityTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('riwalenn')
            ->setEmail('user@gmail.com')
            ->setPassword('FM<gbO!SI)FD?ASy5"')
            ->setRoles(array('ROLE_USER'));
    }

    public function assertHasErrors(User $user, int $number = 0)
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

    /* tests */

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
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

    public function testInvalidBlankRoles()
    {
        $this->assertHasErrors($this->getEntity()->setRoles([]), 1);
    }

    public function testTypeArrayRoles()
    {
        $this->assertIsArray($this->getEntity()->getRoles());
    }
}