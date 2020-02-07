<?php

namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Widget\EditionNavWidget;
use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\PostBundle\Widget\CollectionCreationWidget;
use MesClics\PostBundle\Widget\Handler\CollectionCreationWidgetHandler;

class CollectionCreationWidgets extends WidgetsContainer{
    private $collection_creation_handler;
    
    public function __construct(CollectionCreationWidgetHandler $ccwh){
        $this->collection_creation_handler = $ccwh;
    }

    public function initialize($params = array()){
        $this->addWidget(new EditionNavWidget());
        $this->addWidget(new CollectionCreationWidget($params['available_collections'], $this->collection_creation_handler));
    }
}