<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use Symfony\Component\EventDispatcher\Event;

class MesClicsPostPublicationEvent extends Event{
    private $post;

    public function __construct(Post $post){
        $this->post = $post;
    }

    public function getPost(){
        return $this->post;
    }
}