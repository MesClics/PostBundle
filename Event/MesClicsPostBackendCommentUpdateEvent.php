<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use MesClics\CommentBundle\Entity\Comment;
use MesClics\UtilsBundle\Event\MesClicsObjectUpdateEvent;

class MesClicsPostBackendCommentUpdateEvent extends MesClicsObjectUpdateEvent{
    private $post;
    
    public function __construct(Comment $before_update, Comment $after_update, Post $post){
        parent::__construct($before_update, $after_update);

        $this->post = $post;
    }

    public function getPost(){
        return $this->post;
    }
}