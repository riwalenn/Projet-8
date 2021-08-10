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

        $user_to_compare = new User();
        $user_to_compare->setUsername($formData['username']);
        $user_to_compare->setPassword($formData['password']);
        $user_to_compare->setEmail($formData['email']);
        $user_to_compare->setRoles($formData['roles']);
        $form = $this->factory->create(UserType::class, $user_to_compare);
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $formData was modified as expected when the form was submitted
        $this->assertEquals($user_to_compare->getUsername(), $user->getUsername());
        $this->assertEquals($user_to_compare->getPassword(), $user->getPassword());
        $this->assertEquals($user_to_compare->getEmail(), $user->getEmail());
        $this->assertEquals($user_to_compare->getRoles(), $user->getRoles());
    }
}