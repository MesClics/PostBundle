<?php

namespace MesClics\PostBundle\Actions;

use MesClics\PostBundle\Entity\Post;
use MesClics\NavigationBundle\Entity\Action;

class MesClicsPostActions{
    public static function create(Post $post){
        $label = "création d'une nouvelle publication intitulée " . $post->getTitle() . ".";
        $objects = array(
            "post" => $post
        );

        return new Action($label, $objects);
    }
}