<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    CONST CATEGORIES=[
        'Action',
        'Aventure',
        'Horreur',
        'Fantastique',
        'Coréen',
        'Animation',
        'Thriller'
    ];
    public function load(ObjectManager $manager)
    {
        foreach(self::CATEGORIES as $key=>$categoryName) {  
            $category = new Category();  
            $category->setName($categoryName);

            $manager->persist($category);
            $this->addReference('category_' . $categoryName, $category);
        }  
        $manager->flush();
    }
}