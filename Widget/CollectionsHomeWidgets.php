<?php

namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Widget\EditionNavWidget;
use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\PostBundle\Widget\CollectionsListWidget;
use MesClics\PostBundle\Widget\CollectionCreationWidget;
use MesClics\PostBundle\Widget\Handler\CollectionsListWidgetHandler;
use MesClics\PostBundle\Widget\Handler\CollectionCreationWidgetHandler;

class CollectionsHomeWidgets extends WidgetsContainer{
    private $collections_list_handler;
    private $collection_creation_handler;

    public function __construct(CollectionsListWidgetHandler $clwh, CollectionCreationWidgetHandler $ccwh){
        parent::__construct();
        $this->collections_list_handler = $clwh;
        $this->collection_creation_handler = $ccwh;
    }
    
    public function initialize($params = array()){
        $this->addWidget(new EditionNavWidget());
        $this->addWidget(new CollectionsListWidget($this->collections_list_handler));
        $this->addWidget(new CollectionCreationWidget($params['available_collections'], $this->collection_creation_handler));
    }
}