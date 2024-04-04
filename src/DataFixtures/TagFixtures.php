<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Créer un certain nombre de tags
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $tag->setName($faker->word);

            $manager->persist($tag);

            // Cette référence est utilisée pour relier des tags aux voyages dans TripFixtures
            $this->addReference('tag-'.$i, $tag);
        }

        $manager->flush();
    }
}
