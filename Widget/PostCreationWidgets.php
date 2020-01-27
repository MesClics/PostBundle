<?php

namespace MesClics\PostBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\PostBundle\Widget\PostCreationWidget;
use MesClics\PostBundle\Widget\Handler\PostCreationWidgetHandler;

class PostCreationWidgets extends WidgetsContainer{
    protected $post_creation_handler;

    public function __construct(PostCreationWidgetHandler $pcwh){
        parent::__construct();
        $this->post_creation_handler = $pcwh;
    }

    public function initialize($params = array()){
        $this->addWidget(new PostCreationWidget($params['author'], $this->post_creation_handler));
    }
}