<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

Trait TestFixtures
{
    private $usernames = ["sylvie.thibault", "gcaron", "xclerc", "delmas.jules", "camille.joubert", "guy88", "ccolas", "chantal.vasseur"];
    private $rolesUsernames = ["anonyme", "user", "admin"];
    private $email = ["shoareau@pruvost.net", "iauger@marion.org", "ocoulon@benard.fr", "marine.besson@millet.net", "lucas.evrard@hotmail.fr", "jacqueline.payet@ruiz.org", "obecker@richard.com", "mlaporte@orange.fr"];
    private $role = [["ROLE_USER"], ["ROLE_ADMIN"]];
    private $titles = [
        "Sit placeat quod",
        "Vitae eaque modi deserunt voluptas similique",
        "Sit dolorem possimu.",
        "Eligendi nihil ducimus sed dolor enim",
        "Nostrum placeat aut ex eaque",
        "Quae doloremque ut est hic",
        "Magni molestiae odit est",
        "Maxime deleniti dolores",
        "Sequi aut omnis aut nulla",
        "Quasi illum maiores corporis recusandae",
        "Itaque nesciunt voluptas et qui",
        "Debitis eum omnis qui",
        "Et itaque tempora quod esse",
        "Nostrum dolores voluptatem",
        "Veniam beatae dolores enim eum",
        "Eligendi similique veritatis",
        "Qui iure aliquid iste enim",
        "Non eius qui eos ullam",
        "Sequi quae omnis sapiente",
        "Soluta ipsa natus",
        "Incidunt sit est",
        "Molestiae excepturi reprehenderit et",
        "Eos libero sit eum deserunt",
        "Vitae eos sed magnam repellendus",
        "Quas hic molestiae",
        "In est est animi corrupti",
        "Aliquam consequuntur enim dignissimos illo",
        "Animi unde praesentium voluptatum et reprehenderit",
        "Aut ipsa ab odio",
        "Consequatur quaerat iste voluptatem",
        "Accusantium sint veniam",
        "Ullam ad assumenda et"
    ];
    private $contents = [
        "Placeat vero labore aut repellat. Cumque ut id deserunt occaecati aut. Nemo ea iste voluptas architecto odio dolorum.",
        "Ut dolores perspiciatis magnam omnis et molestias dolor. Vitae autem sit hic suscipit exercitationem. Qui commodi dicta eveniet.",
        "Mollitia corporis enim consectetur aut dignissimos incidunt quia. Veniam recusandae amet repellendus consequatur et quae dolorem ut. Sit eius et nostrum et fugit voluptates.",
        "Quibusdam non natus natus voluptatem consequatur consectetur natus. Fugit eum sit nisi nemo corporis repellat ut. Rem animi aliquam dolor quis sunt.",
        "Veniam eius inventore ut. Est qui sit reiciendis enim dolores doloremque esse. Veniam doloremque dolores voluptas sed id.",
        "Maxime et et suscipit provident quis debitis. Et est voluptas adipisci.",
        "Esse odit facilis explicabo eaque. Maxime nemo autem non. Aliquam cupiditate et ut sed minus.",
        "At neque officia sunt qui nesciunt. Sint ut dolorum aut.",
        "Dolor sint explicabo culpa dolores tempora. Officia atque explicabo dicta provident magnam iure.",
        "Odio neque est omnis consequatur et. Facilis sed voluptas repellendus voluptas. Aliquid vitae voluptatem nam hic harum qui dolores.",
        "Consequatur ullam voluptate rem modi. Natus officiis distinctio corrupti laborum temporibus fugiat voluptas. Eum et officiis dolorum qui iure fugit.",
        "Iure sed adipisci omnis odit atque ut praesentium. Explicabo molestiae rerum asperiores. Voluptates voluptas id voluptas quod accusantium repellendus.",
        "Et reiciendis nihil id. Amet eum delectus rerum iste praesentium dolores tenetur.",
        "Voluptatem corrupti similique beatae nam. Id aspernatur aut ut fugit. In dolore nam nisi ex asperiores suscipit.",
        "Earum et in consequuntur earum. Similique omnis officiis non cumque. Et dolorem excepturi harum consequuntur quam totam itaque.",
        "Provident optio alias perferendis soluta qui eius. Excepturi nemo eligendi ipsam omnis repellendus ex quia. Dolores tempora nostrum unde.",
        "Quo ut quas quia exercitationem itaque. In rem aut impedit et qui omnis tenetur.",
        "Voluptas incidunt sed ea corrupti possimus. Dolore perspiciatis cumque voluptatibus repudiandae. Ea qui rerum eligendi qui quia aut quisquam.",
        "Quasi accusantium at est est non. Qui sapiente eaque qui nemo. Tempore ullam id aspernatur.",
        "Voluptas voluptatem placeat ut autem sunt nihil enim. Vero consequatur ut enim architecto. Deserunt at rerum et ipsam et aliquid id.",
        "Earum autem ut quia sequi. Ut dolores eaque deleniti necessitatibus ad.",
        "Nostrum at aut minus. In vitae neque quasi nemo sint sit ad. Minus praesentium eum corrupti ullam voluptatem minus.",
        "Eum ratione sit ipsam dolorem at nobis ea. Maxime nisi aliquam ad blanditiis. Eveniet ab incidunt ut est.",
        "Delectus vel aperiam non dignissimos expedita eos. Blanditiis minima qui ducimus doloremque. Quae rerum eos quis ut.",
        "Ut incidunt architecto ut dolorum. Unde eos quia corrupti aut eum. Repellat qui dolor quia.",
        "Culpa qui tenetur harum saepe nemo maxime. Veritatis voluptates vel quae dolorem qui non porro consequatur. Quo earum aut ipsum rem molestias et sequi.",
        "Quis enim iure quas nostrum. Nesciunt aut soluta quisquam accusantium aut reiciendis nostrum reiciendis.",
        "Non exercitationem qui incidunt repudiandae. Est repellat id velit omnis ut enim.",
        "Illo at soluta odit et. Est expedita quisquam consequatur adipisci ut doloremque.",
        "Libero et eaque excepturi autem praesentium. Itaque sequi debitis ut incidunt sapiente qui placeat.",
        "Natus nisi voluptatum aliquid rerum reprehenderit amet. Consequatur quis est animi et voluptas.",
        "Laboriosam delectus ut dolores ut quos. Sunt atque deleniti qui incidunt expedita necessitatibus."
    ];
    private $dates = [
        "2021-07-30 08:56:30",
        "2021-07-30 08:48:21",
        "2021-07-30 06:13:46",
        "2021-07-28 12:20:29",
        "2021-07-27 16:22:06",
        "2021-07-25 12:25:26",
        "2021-07-23 20:55:50",
        "2021-07-21 21:04:56",
        "2021-07-21 13:43:01",
        "2021-07-19 16:24:21",
        "2021-07-18 04:37:05",
        "2021-07-17 00:13:43",
        "2021-07-14 05:52:52",
        "2021-07-13 06:29:29",
        "2021-07-11 07:19:32",
        "2021-07-11 05:05:50",
        "2021-07-10 18:38:45",
        "2021-07-09 02:53:09",
        "2021-07-07 07:03:09",
        "2021-07-04 02:56:06",
        "2021-06-29 20:24:32",
        "2021-06-27 07:03:04",
        "2021-06-22 01:14:01",
        "2021-06-17 17:38:40",
        "2021-06-12 00:08:34",
        "2021-06-09 00:21:13",
        "2021-06-05 14:34:17",
        "2021-06-01 14:54:19"
    ];
    private $manager;

    public function __construct($usernames, $rolesUsernames, $email, $role, $titles, $contents, $dates)
    {
        $this->usernames = $usernames;
        $this->rolesUsernames = $rolesUsernames;
        $this->email = $email;
        $this->role = $role;
        $this->titles = $titles;
        $this->contents = $contents;
        $this->dates = $dates;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadTaskNotAssigned();
        $this->loadUserAndTask();
        $this->loadRolesUsers();

        $manager->flush();
    }

    public function loadUserAndTask()
    {
        $fakeUser = array_rand(array_flip($this->usernames));
        $fakeEmail = array_rand(array_flip($this->email));
        for ($i = 1; $i < 5; $i++) {
            $user = new User();
            $user->setUsername($fakeUser)
                ->setEmail($fakeEmail)
                ->setPassword($fakeUser)
                ->setRoles(['ROLE_USER']);

            $this->manager->persist($user);

            for ($j = 0; $j < 3; $j++) {
                $title = array_rand(array_flip($this->titles));
                $content = array_rand(array_flip($this->contents));
                $createdAt = array_rand(array_flip($this->dates));
                $isDone = array_rand(array_flip([0, 1]));
                $task = new Task();
                $task->setTitle($title)
                    ->setContent($content)
                    ->setCreatedAt(new \DateTime($createdAt))
                    ->setIsDone($isDone)
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
            $title = array_rand(array_flip($this->titles));
            $content = array_rand(array_flip($this->contents));
            $createdAt = array_rand(array_flip($this->dates));
            $isDone = array_rand(array_flip([0, 1]));
            $task = new Task();
            $task->setTitle($title)
                ->setContent($content)
                ->setCreatedAt(new \DateTime($createdAt))
                ->setIsDone($isDone);
            $this->manager->persist($task);
        }
        $this->manager->flush();
    }

    public function loadRolesUsers()
    {
        foreach ($this->rolesUsernames as $userRole) {
            $user = new User();
            $user->setUsername($userRole)
                ->setEmail($userRole."@gmail.com")
                ->setPassword($userRole)
                ->setRoles($userRole == "admin" ? $this->role[1] : $this->role[0]);
            $this->manager->persist($user);

            for ($i = 0; $i < 5; $i++) {
                $title = array_rand(array_flip($this->titles));
                $content = array_rand(array_flip($this->contents));
                $createdAt = array_rand(array_flip($this->dates));
                $isDone = array_rand(array_flip([0, 1]));
                $task = new Task();
                $task->setTitle($title)
                    ->setContent($content)
                    ->setCreatedAt(new \DateTime($createdAt))
                    ->setIsDone($isDone)
                    ->setUser($user);
                $this->manager->persist($task);
            }

            $this->manager->flush();
        }
    }
}