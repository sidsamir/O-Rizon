<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use DateTimeImmutable;

class PostFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 250; $i++) {
            $post = new Post();
            $post->setTitle('Post ' . $i);
            $post->setSummary('This is a summary for post ' . $i);
            $post->setImageName('https://picsum.photos/1000/500');
            $post->setBudget(mt_rand(100, 1000));
            $post->setCity('City ' . $i);
            $post->setAddress('1234 Main St, City ' . $i);
            $post->setVote(mt_rand(0, 100));
            $post->setDate(new DateTimeImmutable('now'));
            $post->setCreatedAt(new DateTimeImmutable('now'));

            $userReference = 'user-' . rand(0, 50);
            $participant = $this->getReference($userReference);
            $post->setParticipant($participant);

            $tripReference = 'trip-' . rand(0, 150);
            $trip = $this->getReference($tripReference);
            $post->setTrip($trip);
            $manager->persist($post);
        }




        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TripFixtures::class,
        ];
    }
}


