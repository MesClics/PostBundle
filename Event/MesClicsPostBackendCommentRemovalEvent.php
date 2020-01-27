<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use MesClics\CommentBundle\Entity\Comment;
use Symfony\Component\EventDispatcher\Event;

class MesClicsPostBackendCommentRemovalEvent extends Event{
    private $post;
    private $comment;

    public function __construct(Comment $comment, Post $post){
        $this->post = $post;
        $this->comment = $comment;
    }

    public function getPost(){
        return $this->post;
    }

    public function getComment(){
        return $this->comment;
    }
}