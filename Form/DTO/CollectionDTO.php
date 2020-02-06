<?php

namespace MesClics\PostBundle\Form\DTO;

use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class CollectionDTO extends DataTransportObjectToEntity{
    private $name;
    private $entity;
    private $description;

    public function getMappingArray(){
        return array(
            new MappingArrayItem('name', array('getName', 'setName')),
            new MappingArrayItem('entity', array('getEntity', 'setEntity')),
            new MappingArrayItem('description', array('getDescription', 'setDescription'))
        );
    }

    public function getName(){
        return $this->name;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getEntity(){
        return $this->entity;
    }

    public function setEntity(string $entity){
        $this->entity = $entity;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription(string $description){
        $this->description = $description;
    }
}