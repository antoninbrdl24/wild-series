<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public CONST PROGRAM=[
        [
            'title' => 'Walking Dead',
            'synopsis' => 'Des zombies envahissent la terre',
            'category_reference' => 'category_Action',
        ],
        [
            'title' => 'Stranger Things',
            'synopsis' => 'Des événements étranges se produisent dans une petite ville',
            'category_reference' => 'category_Fantastique',
        ],
        [
            'title' => 'Breaking Bad',
            'synopsis' => 'Un professeur de chimie devient un fabricant de méthamphétamine',
            'category_reference' => 'category_Thriller',
        ],
        [
            'title' => 'Game of Thrones',
            'synopsis' => 'Une lutte pour le trône de fer dans un monde fantastique',
            'category_reference' => 'category_Fantastique',
        ],
        [
            'title' => 'MindHunter',
            'synopsis' => 'Les aventures d\'un psychologue qui dresse le portrait des serial killer pour la FBI',
            'category_reference' => 'category_Thriller',
        ]
    ];
    public function load(ObjectManager $manager)
    {
        foreach(self::PROGRAM as $newProgram){
        $program = new Program();
        $program->setTitle($newProgram['title']);
        $program->setSynopsis($newProgram['synopsis']);
        $program->setCategory($this->getReference($newProgram['category_reference']));
        $manager->persist($program);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          CategoryFixtures::class,
        ];
    }


}