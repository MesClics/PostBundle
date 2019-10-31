<?php

namespace MesClics\PostBundle\Actions;

use MesClics\PostBundle\Entity\Post;
use MesClics\NavigationBundle\Entity\Action;

class MesClicsPostActions{
    public static function creation(Post $post){        
        switch ($post->getVisibilite()){
            case "public":
            $visibilite = "publique";
            break;

            case "private":
            $visibilite = "privée";
            break;
        }
        
        if($post->isDraft()){
            $label = "ajout d'un brouillon de publication " . $visibilite . " intitulé " . $post->getTitle() . ".";
        }
        $label = "création d'une nouvelle publication " . $visibilite . " intitulée " . $post->getTitle() . ".";
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