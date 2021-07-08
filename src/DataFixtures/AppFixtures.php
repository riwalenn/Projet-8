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
        $faker = Factory::create('fr_FR');
        $this->manager = $manager;
        for ($i = 0; $i < 4; $i++) {
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
}
