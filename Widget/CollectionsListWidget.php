<?php

namespace MesClics\PostBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\PostBundle\Widget\Handler\CollectionsListWidgetHandler;

class CollectionsListWidget extends Widget{
    public function __construct(CollectionsListWidgetHandler $handler){
        $this->handler = $handler;
    }
    
    public function getName(){
        return 'collections_list';
    }

    public function getTemplate(){
        return 'MesClicsPostBundle:Widgets:collections-list.html.twig';
    }

    public function getVariables(){
        return array(
            'collections' => $this->handler->getCollections()
        );
    }
}