<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-reference';

    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 51; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email);
            $user->setRoles(['ROLE_USER']);
            $password = $this->encoder->hashPassword($user, 'password');
            $user->setPassword($password);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setIsVerified($faker->boolean);
            $user->setBiographie($faker->text);
            $user->setUsername($faker->userName);
            $user->setCreatedAt($faker->dateTimeThisYear);

            $manager->persist($user);
            $this->addReference('user-' . $i, $user);

        }

        $manager->flush();
    }
}
