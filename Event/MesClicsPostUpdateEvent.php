<?php

namespace MesClics\PostBundle\Event;

use MesClics\PostBundle\Entity\Post;
use MesClics\UtilsBundle\Event\MesClicsObjectUpdateEvent;

class MesClicsPostUpdateEvent extends MesClicsObjectUpdateEvent{
    public function __construct(Post $before_update, Post $after_update){
        parent::__construct($before_update, $after_update);
    }
}