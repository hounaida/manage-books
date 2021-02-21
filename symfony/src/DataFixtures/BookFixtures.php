<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Faker\Factory;
use Faker\Generator;

class BookFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private static $bookImages = [
        "ibiza-bohemia.jpg",
        "martian-andy-wayer.jpg",
        "muscle.jpg",
        "the-dress-and-the-girl.jpg"
    ];
    private $fileUploader;
    /** @var Generator */
    private $faker;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 11; $i++) {
            $book = new Book();
            $book->setTitle('book ' . $i);
            $imageFilename = $this->fakeUploadImage();
            $book->setCover($imageFilename);
            $book->setDescritpion('Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Fusce vestibulum sollicitudin nisl, eget rutrum sem vulputate at. 
                Nullam scelerisque dolor eu nulla vehicula iaculis. 
            ');
            $randBool = (bool)random_int(0, 1);
            $book->setStatus($randBool);
            $book->setAuthor('author'. $i);
            $manager->persist($book);
        }

        $manager->flush();
    }

    private function fakeUploadImage(): string
    {
        $randomImage = $this->faker->randomElement(self::$bookImages);
        $fs = new Filesystem();
        $targetPath = sys_get_temp_dir().'/'.$randomImage;
        $fs->copy(__DIR__.'/Images/'.$randomImage, $targetPath, true);

        return $this->fileUploader->upload(new File($targetPath), null);
    }

    public static function getGroups(): array
    {
        return ['book'];
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
