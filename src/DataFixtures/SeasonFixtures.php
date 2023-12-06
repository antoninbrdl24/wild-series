<?php
namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS=5;

    public function load(ObjectManager $manager)
    {   
        $faker = Factory::create();

        foreach (ProgramFixtures::PROGRAM as $program) {
            for ($seasonNumber = 1; $seasonNumber <= 5; $seasonNumber++) {
                $season = new Season();
                $season->setProgram($this->getReference('program_'.$program['title']));
                $season->setNumber($seasonNumber);
                $season->setDescription($faker->realText(200));
                $season->setYear($faker->year());
                $this->addReference('program_' . $program['title']. '_season'. $seasonNumber, $season);
                $manager->persist($season);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}
