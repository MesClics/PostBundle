<?php

namespace MesClics\PostBundle\Form\DTO;

use MesClics\PostBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Entity\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\PostBundle\Form\DTO\PostCollectionDTO;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class PostDTO extends DataTransportObjectToEntity{
    private $title;
    private $content;
    private $date_publication;
    private $date_peremption;
    private $visibilite;
    private $collections_select;
    private $newcollections;
    private $old_collections;

    public function __construct(){
        parent::__construct();
        $this->collections_select = array();
    }

    public function getMappingArray(){
        $title_mapping = new MappingArrayItem("title", array("getTitle", "setTitle"));
        $content_mapping = new MappingArrayItem("content", array("getContent", "setContent"));
        $date_publication_mapping = new MappingArrayItem("date_publication", array("getDatePublication", "setDatePublication"));
        $date_peremption_mapping = new MappingArrayItem("date_peremption", array("getDatePeremption", "setDatePeremption"));
        $visibilite_mapping = new MappingArrayItem("visibilite", array("getVisibilite", "setVisibilite"));
        
        $mapping_array = array(
            $title_mapping,
            $content_mapping,
            $date_publication_mapping,
            $date_peremption_mapping,
            $visibilite_mapping
        );

        return $mapping_array;
    }

    public function beforeMappingFrom(Post $entity){
        $collections = $entity->getCollections();
        foreach($collections as $collection){
            $this->collections_select[] = $collection;
        }
    }

    public function afterMappingFrom(Post $entity){
        // hydrate old_collections property before any update
        $this->old_collections = $entity->getCollections()->toArray();
    }

    public function beforeMappingTo(Post $entity){

        //add collections not already saved in database
        if($this->getCollectionsSelect()){
            foreach($this->getCollectionsSelect() as $collection){
                if(!$entity->getCollections()->contains($collection)){
                    $entity->addCollection($collection);
                }
            }
        }

        //remove collections saved in database that are not anymore in $dto
        foreach($entity->getCollections() as $collection){
            if(!in_array($collection, $this->collections_select)){
                $entity->removeCollection($collection);
            }
        }

        if($this->getNewcollections()){
            foreach($this->getNewcollections() as $collection){
                //on crée un nvl objet Collection dont l'attribut entité est défini à 'post'
                $new_collec = new Collection('post');
                //auquel on transmet les infos name et description du formulaire
                $new_collec->setName($collection->getName());
                $new_collec->setDescription($collection->getDescription());
                //on persiste notre objet
                $this->entity_manager->persist($new_collec);
                //on ajoute la nouvelle collection à notre objet post
                $entity->addCollection($new_collec);
            }
        }
    }

    public function getTitle(){
        return $this->title;
    }
    public function setTitle(string $title){
        $this->title = $title;
    }

    public function getContent(){
        return $this->content;
    }
    public function setContent(string $content = null){
        if($content){
            $this->content = $content;
        }
    }
    
    public function getDatePublication(){
        return $this->date_publication;
    }
    public function setDatePublication(\DateTime $date = null){
        if($date){
            $this->date_publication = $date;
        }
    }

    public function getDatePeremption(){
        return $this->date_peremption;
    }
    public function setDatePeremption(\DateTime $date = null){
        if($date){
            $this->date_peremption = $date;
        }
    }

    public function getVisibilite(){
        return $this->visibilite;
    }
    public function setVisibilite(string $visibilite){
        $this->visibilite = $visibilite;
    }

    public function getCollectionsSelect(){
        return $this->collections_select;
    }
    public function setCollectionsSelect(array $collections = null){
        if($collections){
            $this->collections_select = $collections;
        }
    }

    public function getNewcollections(){
        return $this->newcollections;
    }

    public function setNewcollections(array $collections = null){
        if($collections){
            $this->newcollections = $collections;
        }
    }

    public function getOldCollections(){
        return $this->old_collections;
    }

    public function setOldCollections($collections){
        $this->old_collections = $collections;
    }
}