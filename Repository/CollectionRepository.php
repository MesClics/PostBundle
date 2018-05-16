<?php

namespace MesClics\PostBundle\Repository;

/**
 * CollectionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CollectionRepository extends \Doctrine\ORM\EntityRepository{
    public function getCollectionsQB($of = null){
        // TODO: ajouter le critère User (ne sélectionner que les collections que peut voir l'utilisateur)
        $qb = $this
        ->createQueryBuilder('collection')
        ->orderBy('collection.entity');

        if($of){
            $qb
            ->andWhere('collection.entity = :entity')
                ->setParameter('entity', $of);
        }

        return $qb;
    }

    public function getCollections($of = null){
        $qb = $this->getCollections($of);
        return $qb->getQuery()->getResult();
    }

    public function countCollections($of = null){
        $qb = $this->getCollectionsQB($of);
        $qb
        ->select('COUNT(collection)');
        return $qb->getQuery()->getSingleScalarResult();
    }
}
