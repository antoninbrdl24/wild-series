<?php
namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Program; 
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Repository\ProgramRepository;

use Faker\Factory;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {   
        $faker = Factory::create();
        $programRepository = $manager->getRepository(Program::class);
        $programs = $programRepository->findAll();

        for ($actorNumber= 1; $actorNumber <= 10; $actorNumber++) {
                $actor = new Actor();
                $actor->setName($faker->name);
                for ($i = 0; $i < 3; $i++) {
                    $programReference = 'program_'.$faker->randomElement($programs)->getTitle();
                    $actor->addProgram($this->getReference($programReference));
                }
                $manager->persist($actor);
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