<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Collection;
use Symfony\Component\EventDispatcher\Event;

class MesClicsCollectionCreationEvent extends Event{
    private $collection;

    public function __construct(Collection $collection){
        $this->collection = $collection;
    }

    public function getCollection(){
        return $this->collection;
    }
}

