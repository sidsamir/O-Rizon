<?php

namespace App\DataFixtures;

use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FriendshipFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 200; $i++) {
            $friendship = new Friendship();
            $friendship->setStatus(Friendship::STATUS_ACCEPTED);
            $friendship->setCreatedAt(new \DateTimeImmutable());
            $friendship->setFriendAt(new \DateTimeImmutable());

            // Simuler des utilisateurs existants comme requester et receiver
            // Assurez-vous que UserFixtures crée suffisamment d'utilisateurs et les enregistre avec addReference
            $requester = $this->getReference('user-' . rand(0, 9));
            $receiver = $this->getReference('user-' . rand(10, 19));

            $friendship->setRequester($requester);
            $friendship->setReceiver($receiver);

            $manager->persist($friendship);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
