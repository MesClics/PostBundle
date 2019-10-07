<?php
namespace MesClics\PostBundle\Form\DTO;

use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class PostCollectionDTO extends DataTransportObjectToEntity{
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

    public function getMappingArray(){
        $name_mapping = new MappingArrayItem("name", "getName", "setName");
        $description_mapping = new MappingArrayItem("description", "getDescription", "setDescription");

        $mapping_array = array(
            $name_mapping,
            $description_mapping
        );
        return $mapping_array;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function setDescription(string $description){
        $this->description = $description;
    }
}