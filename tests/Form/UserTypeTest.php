<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'username' => 'test_username',
            'password' => 'test_password',
            'email'    => 'test_email',
            'roles'    => ["ROLE_USER"]
        ];

        $user = new User();
        $user->setUsername($formData['username']);
        $user->setPassword($formData['password']);
        $user->setEmail($formData['email']);
        $user->setRoles($formData['roles']);

        $userToCompare = new User();
        $userToCompare->setUsername($formData['username']);
        $userToCompare->setPassword($formData['password']);
        $userToCompare->setEmail($formData['email']);
        $userToCompare->setRoles($formData['roles']);
        $form = $this->factory->create(UserType::class, $userToCompare);
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $formData was modified as expected when the form was submitted
        $this->assertEquals($userToCompare->getUsername(), $user->getUsername());
        $this->assertEquals($userToCompare->getPassword(), $user->getPassword());
        $this->assertEquals($userToCompare->getEmail(), $user->getEmail());
        $this->assertEquals($userToCompare->getRoles(), $user->getRoles());
    }
}