<?php

namespace MesClics\PostBundle\Actions;

use MesClics\PostBundle\Entity\Post;
use MesClics\NavigationBundle\Entity\Action;

class MesClicsPostActions{
    public static function creation(Post $post){
        $label = "création d'une nouvelle publication intitulée " . $post->getTitle() . ".";
        $objects = array(
            "post" => $post
        );

        return new Action($label, $objects);
    }

    public static function update(Post $post){
        $label = "modification de la publication " . $post->getTitle() . ".";
        $objects = array(
            "post" => $post
        );

        return new Action($label, $objects);
    }

    public static function removal(Post $post){
        $label = "suppression de la publication " . $post->getTitle() . ".";
    }

}