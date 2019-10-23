<?php
namespace MesClics\PostBundle\Form\DTO;

use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class PostCollectionDTO{
    public $name;
    public $description;

    public function __construct(string $name = null, string $description = null){
        if($name){
            $this->name = $name;
        }
        if($description){
            $this->description = $description;
        }
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function setDescription(string $description){
        $this->description = $description;
    }

    public function getName(){
        return $this->name;
    }

    public function getDescription(){
        return $this->description;
    }
}