<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\UtilsBundle\Event\MesClicsObjectUpdateEvent;

class MesClicsPostCategorizationEvent extends MesClicsObjectUpdateEvent{
    public function __construct(Post $before_update, Post $after_update){
        parent::__construct($before_update, $after_update);
    }

    /**
     * get all the new categories that the post has been assigned to
     */
    public function getNewCollections(){
        dump($this->before_update); dump($this->after_update);
        $filteredCollection = $this->after_update->getCollections()->filter(function($collection){
            if(!$this->before_update->getCollections()->contains($collection)){
                return true;
            }
        });

        dump($filteredCollection); die();
        

        return $filteredCollection;

        // $diff = array_diff($this->after_update->getCollections()->toArray(), $this->before_update->getCollections()->toArray());

        // return new ArrayCollection($diff);
    }

    /**
     * get all the categories that the post has been removed from
     */
    public function getOldCollections(){
        $filteredCollection = $this->before_update->getCollections()->filter(function($collection){
            if(!$this->after_update->getCollections()->contains($collection)){
                return true;
            }
        });
        

        return $filteredCollection;
    }

    /**
     * tells if the post has been assigned to one or more new categories or/and if it has been removed from one or more categories
     * 
     * @return array of bool with first entry respond to the answer :  "has it been assigned to one or more categories ?" and the second entry respond to the answer : "has it been removed from one or more categories ?"
     */
    public function getCategorizationType(){
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