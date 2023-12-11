<?php
namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS=5;

    protected $slugger;

    public function __construct(SluggerInterface $slugger)
        {
            $this->slugger = $slugger;
        }
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

                $slug = $this->slugger->slug($seasonNumber);
                $season->setSlug($slug);

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
