<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public CONST EPISODES = [
        [
            'number' => 1,
            'title' => 'titre épisode 1',
            'synopsis' => 'synopsis saison 1',
            'season_reference' => 'season_program_Walking_Dead_1',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (EpisodeFixtures::EPISODES as $episodeData) {
            $seasonReference = $episodeData['season_reference'];
            $season = $this->getReference($seasonReference);

            for ($i = 1; $i <= 3; $i++) {
                $episode = new Episode();
                $episode->setTitle($episodeData['title']);
                $episode->setNumber($i);
                $episode->setSynopsis($episodeData['synopsis']);
                $episode->setSeason($season);
                $referenceName = sprintf('episode_%d_%d', $season->getNumber(),$i);
                $this->setReference($referenceName, $episode);
                $manager->persist($episode);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}