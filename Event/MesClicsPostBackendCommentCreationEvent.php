<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use MesClics\CommentBundle\Entity\Comment;
use Symfony\Component\EventDispatcher\Event;


class MesClicsPostBackendCommentCreationEvent extends Event{
    private $post;
    private $comment;

    public function __construct(Comment $comment, Post $post){
        $this->comment = $comment;
        $this->post = $post;
    }

    public function getPost(){
        return $this->post;
    }

    public function getComment(){
        return $this->comment;
    }
}