<?php
namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Widget\PostsListWidget;
use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use MesClics\PostBundle\Widget\Handler\PostsListWidgetHandler;

class PostsHomeWidgets extends WidgetsContainer{
    private $posts_list_handler;

    public function __construct(PostsListWidgetHandler $plwh){
        parent::__construct();
        $this->posts_list_handler = $plwh;
    }
    
    public function initialize($params = array()){
        $this->addWidget(new PostsListWidget($this->posts_list_handler));
    }
}