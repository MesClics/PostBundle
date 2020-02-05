<?php

namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Entity\Post;
use MesClicsBundle\Entity\MesClicsUser;
use MesClics\PostBundle\Widget\PostEditWidget;
use MesClics\PostBundle\Widget\EditionNavWidget;
use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\PostBundle\Widget\PostBackendCommentsWidget;
use MesClics\PostBundle\Widget\Handler\PostEditWidgetHandler;
use MesClics\PostBundle\Widget\Handler\PostBackendCommentsWidgetHandler;

class PostUpdateWidgets extends WidgetsContainer{
    protected $post_edit_widget_handler;
    protected $post_backend_comments_widget_handler;
    protected $post;
    protected $user;

    public function __construct(PostEditWidgetHandler $pewh, PostBackendCommentsWidgetHandler $pbcwh){
        parent::__construct();
        $this->post_edit_widget_handler = $pewh;
        $this->post_backend_comments_widget_handler = $pbcwh;
    }

    public function initialize($params = array()){
        $this->addWidget(new EditionNavWidget());
        // if first call or post or user changes
        if($this->post !== $params['post'] || $this->user !== $params['user']){
            $this->setPost($params['post']);
            $this->setUser($params['user']);
            $this->addWidget(new PostEditWidget($this->post, $this->post_edit_widget_handler));
            $this->addWidget(new PostBackendCommentsWidget($this->post, $this->user, $this->post_backend_comments_widget_handler));
        }
    }

    public function setPost(Post $post){
        $this->post = $post;
    }

    public function setUser(MesClicsUser $user){
        $this->user = $user;
    }

    public function getPostEditWidget(){
        return $this->getWidget('mesclics_post_edit');
    }

    public function getPostBackendCommentsWidget(){
        return $this->getWidget('mesclics_post_backend_comments');
    }
}