<?php
namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public CONST SEASONS=[
        [
            'number' => 1,
            'description' => 'Description saison 1',
            'year' => 1999,
            'program_reference' => 'program_Walking_Dead',
        ],
        [
            'number' => 1,
            'description' => 'Description saison 1',
            'year' => 1999,
            'program_reference' => 'program_Stranger_Things',
        ],
        [
            'number' => 1,
            'description' => 'Description saison 1',
            'year' => 1999,
            'program_reference' => 'program_Breaking_Bad',
        ],
        [
            'number' => 1,
            'description' => 'Description saison 1',
            'year' => 1999,
            'program_reference' => 'program_Game_of_Thrones',
        ],
        [
            'number' => 1,
            'description' => 'Description saison 1',
            'year' => 1999,
            'program_reference' => 'program_MindHunter',
        ],
    ];
    public function load(ObjectManager $manager)
    {
        foreach (self::SEASONS as $seasonData) {
            for ($i = 1; $i <= 3; $i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setDescription($seasonData['description']);
                $season->setYear($seasonData['year']);
                $season->setProgram($this->getReference($seasonData['program_reference']));
                $manager->persist($season);
                $referenceName = 'season_' . str_replace(' ', '_', $seasonData['program_reference']) . '_'. $i;
                $this->setReference($referenceName, $season);
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
