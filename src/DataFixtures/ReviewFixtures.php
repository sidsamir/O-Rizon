<?php

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Assumons que vous avez déjà 30 voyages et 20 utilisateurs
        for ($i = 0; $i < 250; $i++) {
            $review = new Review();
            $review->setTitle($faker->sentence)
                ->setSummary($faker->text(200))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt($faker->boolean(70) ? new \DateTimeImmutable() : null); // 70% de chance d'avoir une date de mise à jour

            $userReference = 'user-' . rand(0, 50);
            $participant = $this->getReference($userReference);
            $review->setParticipant($participant);

            $tripReference = 'trip-' . rand(0, 150);
            $trip = $this->getReference($tripReference);
            $review->setTrip($trip);


            $manager->persist($review);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TripFixtures::class,
            UserFixtures::class,
        ];
    }
}
