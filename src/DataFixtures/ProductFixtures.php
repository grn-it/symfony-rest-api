<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Tommy Hilfiger / Elevated Leather Trainers');
        $product->setDescription(
            'A printed flag on the resilient vulcanised rubber outsole adds'.
            'a dash of signature colour to these leather trainers.'
        );
        $product->setPrice(115);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Nike / Cosmic Unity 2');
        $product->setDescription(
            'Celebrate the love and joy of the game with the Nike Cosmic Unity 2.'.
            'Made with at least 20% recycled content by weight, it provides enhanced'.
            'responsiveness and support. This shoe will help keep you fresh and secure'.
            'without overloading it with extra ounces, so that you can focus on locking'.
            'down the perimeter defensively or starting the fast break after a rebound.'
        );
        $product->setPrice(240);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Adidas / Ultraboost Light Running Shoes');
        $product->setDescription(
            'Experience epic energy with the new Ultraboost Light, our lightest Ultraboost ever.'.
            'The magic lies in the Light BOOST midsole, a new generation of adidas BOOST. '.
            'Its unique molecule design achieves the lightest BOOST foam to date and boasts a 10%'.
            'lower carbon footprint than previous models (for more information see FAQs below). '.
            'With hundreds of BOOST capsules bursting with energy and ultimate cushioning and comfort,'.
            'some feet really can have it all.'
        );
        $product->setPrice(160);
        $manager->persist($product);

        $manager->flush();
    }
}
