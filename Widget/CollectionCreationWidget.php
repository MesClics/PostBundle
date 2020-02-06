<?php

namespace MesClics\PostBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\PostBundle\Form\DTO\CollectionDTO;
use MesClics\PostBundle\Form\MesClicsCollectionType;
use MesClics\PostBundle\Widget\Handler\CollectionCreationWidgetHandler;

class CollectionCreationWidget extends Widget{
    private $form;
    private $collections;

    public function __construct($available_collections, CollectionCreationWidgetHandler $ccwh){
        $this->handler = $ccwh;

        $collectionDTO = new CollectionDTO();
        $this->form = $this->createForm(MesClicsCollectionType::class, $collectionDTO, array('available_collections' => $available_collections));
    }
    
    public function getName(){
        return "collection_creation";
    }

    public function getTemplate(){
        return 'MesClicsPostBundle:Widgets:collection-new.html.twig';
    }

    public function getVariables(){
        return null;
    }

    public function getCollections(){
        if(!$this->collections){
            $repo = $this->handler->entity_manager->getRepository("MesClicsPostBundle:Collection");
            $this->collections = $repo->getCollections();
        }
        return $this->collections;
    }

    public function getForm(){
        return $this->form;
    }

    public function getFormView(){
        return $this->form->createView();
    }
}