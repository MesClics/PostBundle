<?php
namespace MesClics\PostBundle\Form\DTO;

class PostBackendComment extends DataTransportObjectToEntity{
    public $content;
    public $parent;

    public function __construct(){

    }

    public function getMappingArray(){
        $contentMapping = new MappingArrayItem("content", array("getContent", "setContent"));
        $parentMapping = new MappingArrayItem("parent", array("getParent", "setParent"));

        return array(
            $contentMapping,
            $parentMapping
        );
    }
}