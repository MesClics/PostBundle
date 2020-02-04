<?php

namespace MesClics\PostBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use MesClics\PostBundle\Widget\Handler\PostsListWidgetHandler;

class PostsListWidget extends Widget{

    public function __construct(PostsListWidgetHandler $handler){
        $this->handler = $handler;
        // $post_retriever = $this->initializePostRetriever($request);
    }


    public function getName(){
        return "posts_list";
    }

    public function getTemplate(){
        return "MesClicsPostBundle:Widgets:posts-list.html.twig";
    }

    public function getVariables(){
        return array(
            //on passe les critères de tri à la vue
            'sort_params' => array(
                'order_by' => $this->handler->getPostRetriever()->getOrderBy(),
                'sort' => $this->handler->getPostRetriever()->getOrder()
            ),
            'posts' => $this->getPosts()
        );
    }

    public function getPostRetriever(){
        return $this->handler->getPostRetriever();
    }

    public function getPosts(){
        return $this->getPostRetriever()->getPosts();
    }
}