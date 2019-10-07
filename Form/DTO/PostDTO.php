<?php

namespace MesClics\PostBundle\Form\DTO;

use MesClics\PostBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class PostDTO extends DataTransportObjectToEntity{
    public $title;
    public $content;
    public $date_publication;
    public $date_peremption;
    public $visibilite;
    public $collections_select;
    public $collections_add;
    public $entity_manager;

    public function __construct(EntityManagerInterface $em){
        parent::__construct();
        $this->entity_manager = $em;
        $this->collections_select = array();
    }

    public function getMappingArray(){
        $title_mapping = new MappingArrayItem("title", "getTitle", "setTitle");
        $content_mapping = new MappingArrayItem("content", "getContent", "setContent");
        $date_publication_mapping = new MappingArrayItem("date_publication", "getDatePublication", "setDatePublication");
        $date_peremption_mapping = new MappingArrayItem("date_peremption", "getDatePeremption", "setDatePeremption");
        $visibilite_mapping = new MappingArrayItem("visibilite", "getVisibilite", "setVisibilite");
        
        $mapping_array = array(
            $title_mapping,
            $content_mapping,
            $date_publication_mapping,
            $date_peremption_mapping,
            $visibilite_mapping,
        );

        return $mapping_array;
    }

    public function beforeMappingFrom(Post $entity){
        $collections = $entity->getCollections();
        foreach($collections as $collection){
            $this->collections_select[] = $collection;
        }
    }

    public function beforeMappingTo(Post $entity){
        //add collections not already saved in database
        foreach($this->collections_select as $collection){
            if(!$entity->getCollections()->contains($collection)){
                $entity->addCollection($collection);
            }
        }

        //remove collections saved in database that are not anymore in $dto
        foreach($entity->getCollections() as $collection){
            if(!in_array($collection, $this->collections_select)){
                $entity->removeCollection($collection);
            }
        }

        dump($this->collections_add);
        foreach($this->collections_add as $collection){
            dump("here we are"); die();
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