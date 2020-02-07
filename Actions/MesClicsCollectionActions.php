<?php

namespace MesClics\PostBundle\Actions;

use MesClics\PostBundle\Entity\Collection;
use MesClics\NavigationBundle\Entity\Action;

final class MesClicsCollectionActions{
    public static function creation(Collection $collection){
       $label = "Création d'une nouvelle collection d'objets de type \"" . $collection->getEntity() . "\" nommée " . $collection->getName() . ".";
       $objects = array(
           "collection" => $collection
       ) ;
       return new Action($label, $objects);
    }

    public function update(Collection $before_update, Collection $after_update){
        $label = "Modification de la collection d'objets de type \"" . $after_update->getEntity() . "\" nommée " . $after_update->getName() . ".";
        $objects = array(
           "before_update" => $before_update,
           "after_update" => $after_update
        ) ;
        return new Action($label, $objects);
    }

    public function removal(Collection $collection){
        $label = "suppression de la collection d'objets de type \"" . $collection->getEntity() . "\" nommée " . $collection->getName() . ".";
        $objects = array(
            "collection" => $collection
        ) ;
        return new Action($label, $objects);
    }
}