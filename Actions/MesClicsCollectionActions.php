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
}