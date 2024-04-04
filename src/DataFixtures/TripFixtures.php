<?php

namespace App\DataFixtures;

use App\Entity\Trip;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Generator;

class TripFixtures extends Fixture implements DependentFixtureInterface
{
    private \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 151; $i++) {
            $trip = new Trip();
            $trip->setTitle($this->faker->sentence)
                ->setDescription($this->faker->paragraph)
                ->setImageName('https://picsum.photos/1500/800')  // Utilisation de Lorem Picsum pour une image aléatoire
                ->setBudget($this->faker->numberBetween(1000, 5000))
                ->setStatus($this->faker->randomElement(['En cours', 'Terminé', 'Future']))
                ->setCountry($this->faker->country)
                ->setCity($this->faker->city)
                ->setAddress($this->faker->address)
                ->setDate(new \DateTimeImmutable())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            // Assurez-vous que cela correspond au nombre d'utilisateurs que vous avez créés
            $userReference = 'user-' . rand(0, 50);
            $creator = $this->getReference($userReference);
            $trip->setCreator($creator);

            $tagsCount = mt_rand(1, 3); // Nombre aléatoire de tags par voyage
            for ($j = 0; $j < $tagsCount; $j++) {
                // Obtenir une référence aléatoire à un Tag
                $tagReference = 'tag-' . rand(0, 9);
                $trip->addTag($this->getReference($tagReference));
            }

            $manager->persist($trip);
            $this->addReference('trip-' . $i, $trip);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            UserFixtures::class, // Assurez-vous que les fixtures d'utilisateur sont chargées en premier
        ];
    }
}
