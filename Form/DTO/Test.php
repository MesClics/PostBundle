<?php
namespace MesClics\PostBundle\Form\DTO;

class Test{
    private $name;

    public function setName(string $name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }
}