<?php
namespace MesClics\PostBundle\DataFixtures\ORM;

use MesClics\PostBundle\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use MesClics\UserBundle\DataFixtures\ORM\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface{

    public function load(ObjectManager $manager){

        $now = new \DateTime();
        //+ 3 MONTHS
        $plus3monthsInterval = new \DateInterval('P3M');
        $plus3months = clone $now;
        $plus3months->add($plus3monthsInterval);

        
        //+ 6 MONTHS
        $plus6monthsInterval = new \DateInterval('P6M');
        $plus6months = clone $now;
        $plus6months->add($plus6monthsInterval);

        //+ 6 DAYS
        $plus6daysInterval = new \DateInterval('P6D');
        $plus6days = clone $now;
        $plus6days->add($plus6daysInterval);
        
        //- 6 DAYS
        $minus6daysInterval = new \DateInterval('P6D');
        $minus6daysInterval->invert = 1;
        $minus6days = clone $now;
        $minus6days->add($minus6daysInterval);



        $author = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);

        //TO BE PUBLISHED POST FIXTURES
        //public
        $toBePublishedPublic = new Post();
        $toBePublishedPublic
        ->setTitle("Publié prochainement public")
        ->setDateCreation($now)
        ->setDatePublication($plus3months)
        ->setDatePeremption($plus6months)
        ->setVisibilite("public")
        ->addAuthor($author);
        $manager->persist($toBePublishedPublic);
        //private
        $toBePublishedPrivate = new Post();
        $toBePublishedPrivate
        ->setTitle("Publié prochainement privé")
        ->setDateCreation($now)
        ->setDatePublication($plus3months)
        ->setDatePeremption($plus6months)
        ->setVisibilite("private")
        ->addAuthor($author);
        $manager->persist($toBePublishedPrivate);

        //ONLINE POST
        //public
        $onlinePublic = new Post();
        $onlinePublic
        ->setTitle("En ligne public")
        ->setDateCreation($now)
        ->setDatePublication($now)
        ->setVisibilite("public")
        ->addAuthor($author);
        $manager->persist($onlinePublic);
        //private
        $onlinePrivate = new Post();
        $onlinePrivate
        ->setTitle("En ligne privé")
        ->setDateCreation($now)
        ->setDatePublication($now)
        ->setVisibilite("private")
        ->addAuthor($author);
        $manager->persist($onlinePrivate);

        
        //ONLINE POST WITH PEREMPTION DATE
        //public
        $onlineWithPeremptionPublic = new Post();
        $onlineWithPeremptionPublic
        ->setTitle("En ligne avec date de péremption public")
        ->setDateCreation($now)
        ->setDatePublication($now)
        ->setDatePeremption($plus6months)
        ->setVisibilite("public")
        ->addAuthor($author);
        $manager->persist($onlineWithPeremptionPublic);
        //private
        $onlineWithPeremptionPrivate = new Post();
        $onlineWithPeremptionPrivate
        ->setTitle("En ligne avec date de péremption privé")
        ->setDateCreation($now)
        ->setDatePublication($now)
        ->setDatePeremption($plus6months)
        ->setVisibilite("private")
        ->addAuthor($author);
        $manager->persist($onlineWithPeremptionPrivate);

        //UNPUBLISHED POST
        //public
        $unpublishedPublic = new Post();
        $unpublishedPublic
        ->setTitle("Dépublié public")
        ->setDateCreation($minus6days)
        ->setDatePublication($minus6days)
        ->setDatePeremption($now)
        ->setVisibilite("public")
        ->addAuthor($author);
        $manager->persist($unpublishedPublic);
        //private
        $unpublishedPrivate = new Post();
        $unpublishedPrivate
        ->setTitle("Dépublié privé")
        ->setDateCreation($minus6days)
        ->setDatePublication($minus6days)
        ->setDatePeremption($now)
        ->setVisibilite("private")
        ->addAuthor($author);
        $manager->persist($unpublishedPrivate);

        $manager->flush();
    }

    public function getDependencies(){
        return array(
            UserFixtures::class
        );
    }
}