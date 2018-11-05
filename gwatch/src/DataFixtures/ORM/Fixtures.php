<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use DateTime;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setName('Aleksandr Davydenko')
            ->setUserName('alexandrdavydenko')
            ->setEmail('alexandrdavydenko@yandex.ru');

        $manager->persist($user);

        $project = new Project();
        $project
            ->setTitle('My Botswana project example')
            ->setDescription('Just a brand new project to simulate that smth. exists in the DB')
            ->setCreatedAt(new DateTime('NOW'))
            ->setOwner($user);
        $manager->persist($project);
        $manager->flush();
    }
}