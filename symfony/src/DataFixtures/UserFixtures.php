<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Create author1
        $user = new User();
        $user->setUsername('hounaida');
        $password = $this->encoder->encodePassword($user, 'hounaida');
        $user->setPassword($password);
        $user->setRoles([
            'ROLE_USER',
        ]);

        $manager->persist($user);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
