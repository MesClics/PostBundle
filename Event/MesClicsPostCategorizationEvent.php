<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\UtilsBundle\Event\MesClicsObjectUpdateEvent;

class MesClicsPostCategorizationEvent extends MesClicsObjectUpdateEvent{
    private $post;

    public function __construct(array $before_update, array $after_update, Post $post){

        parent::__construct($before_update, $after_update);
        $this->post = $post;
    }

    /**
     *  get the post
     */
    public function getPost(){
        return $this->post;
    }

    /**
     * get all the new categories that the post has been assigned to
     */
    public function getNewCollections(){
        $filteredCollection = array_filter($this->after_update, function($collection){
            if(!in_array($collection, $this->before_update)){
                return true;
            }
        });
        
        if(!empty($filteredCollection)){
            return $filteredCollection;
        } else{
            return false;
        }
    }

    /**
     * get all the categories that the post has been removed from
     */
    public function getOldCollections(){
        $filteredCollection = array_filter($this->before_update, function($collection){
            if(!in_array($collection, $this->after_update)){
                return true;
            }
        });
        
        if(!empty($filteredCollection)){
            return $filteredCollection;
        } else{
            return false;
        }
    }

    /**
     * tells if the post has been assigned to one or more new collections or/and if it has been removed from one or more categories
     * 
     * @return array of bool with first entry respond to the answer :  "has it been assigned to one or more categories ?" and the second entry respond to the answer : "has it been removed from one or more categories ?"
     */
    public function getCategorizationType(){
        $assigned = null; $removed = null;
        
        if($this->getNewCollections()){
            $assigned = true;
        }

        if($this->getOldCollections()){
            $removed = true;
        }

        return array(
            "assigned_to" => $assigned,
            "removed_from" => $removed
        );
    }
}