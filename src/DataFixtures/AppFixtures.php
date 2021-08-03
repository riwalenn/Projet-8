<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $manager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadTaskNotAssigned();
        $this->loadUserAndTask();
        $this->loadAnonymousUser();
        $this->loadAdminuser();
        $this->loadRoleUser();

        $manager->flush();
    }

    public function loadUserAndTask()
    {
        $faker = Factory::create('fr_FR');
        for ($i = 1; $i < 5; $i++) {
            $user = new User();
            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($this->passwordEncoder->encodePassword($user, $faker->password))
                ->setRoles(['ROLE_USER']);

            $this->manager->persist($user);

            for ($j = 0; $j < 3; $j++) {
                $task = new Task();
                $task->setTitle($faker->sentence(4, true))
                    ->setContent($faker->paragraph(2))
                    ->setCreatedAt($faker->dateTimeBetween('- 2 months'))
                    ->setIsDone($faker->boolean)
                    ->setUser($user);
                $this->manager->persist($task);
            }
        }
        $this->manager->flush();
    }

    public function loadTaskNotAssigned()
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(4, true))
                ->setContent($faker->paragraph(2))
                ->setCreatedAt($faker->dateTimeBetween('- 2 months'))
                ->setIsDone($faker->boolean);
            $this->manager->persist($task);
        }
        $this->manager->flush();
    }

    public function loadAnonymousUser()
    {
        $faker = Factory::create('fr_FR');
        $user = new User();
        $user->setUsername("anonyme")
            ->setEmail("anonyme@gmail.com")
            ->setPassword($this->passwordEncoder->encodePassword($user, "anonyme"))
            ->setRoles(['ROLE_USER']);
        $this->manager->persist($user);

        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(4, true))
                ->setContent($faker->paragraph(2))
                ->setCreatedAt($faker->dateTimeBetween('- 2 months'))
                ->setIsDone($faker->boolean)
                ->setUser($user);;
            $this->manager->persist($task);
        }

        $this->manager->flush();
    }

    public function loadAdminuser()
    {
        $faker = Factory::create('fr_FR');
        $user = new User();
        $user->setUsername("admin")
            ->setEmail("admin@gmail.com")
            ->setPassword($this->passwordEncoder->encodePassword($user, "admin"))
            ->setRoles(['ROLE_ADMIN']);
        $this->manager->persist($user);

        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(4, true))
                ->setContent($faker->paragraph(2))
                ->setCreatedAt($faker->dateTimeBetween('- 2 months'))
                ->setIsDone($faker->boolean)
                ->setUser($user);;
            $this->manager->persist($task);
        }

        $this->manager->flush();
    }

    public function loadRoleUser()
    {
        $faker = Factory::create('fr_FR');
        $user = new User();
        $user->setUsername("user")
            ->setEmail("user@gmail.com")
            ->setPassword($this->passwordEncoder->encodePassword($user, "user"))
            ->setRoles(['ROLE_USER']);
        $this->manager->persist($user);

        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(4, true))
                ->setContent($faker->paragraph(2))
                ->setCreatedAt($faker->dateTimeBetween('- 2 months'))
                ->setIsDone($faker->boolean)
                ->setUser($user);;
            $this->manager->persist($task);
        }

        $this->manager->flush();
    }
}
