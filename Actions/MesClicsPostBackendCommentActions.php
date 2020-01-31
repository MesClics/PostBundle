<?php

namespace MesClics\PostBundle\Actions;

use MesClics\PostBundle\Entity\Post;
use MesClics\CommentBundle\Entity\Comment;
use MesClics\NavigationBundle\Entity\Action;

class MesClicsPostBackendCommentActions{
    public static function onCreation(Comment $comment, Post $post){
        $message = "Publication d'un commentaire pour la puiblication " . $post->getTitle();

        $objects = array(
            "post" => $post,
            "comment" => $comment
        );

        return new Action($message, $objects);
    }

    public static function onUpdate(Comment $comment, Post $post){
        $message = "Modification du commentaire publié le " . $comment->getUpdatedAt()->format("d/m/Y \à H:i") . " pour la puiblication " . $post->getTitle();

        $objects = array(
            "post" => $post,
            "comment" => $comment
        );

        return new Action($message, $objects);
    }

    public function onRemoval(Comment $comment, Post $post){
        $message = "Suppression du commentaire publié par " . $comment->getAuthor()->getUsername() . " le " . $comment->getUpdatedAt()->format("d/m/Y à H:i") . " pour la puiblication " . $post->getTitle();

        $objects = array(
            "post" => $post,
            "comment" => $comment
        );

        return new Action($message, $objects);
    }

    public static function onPin(Comment $comment, Post $post){
        if($comment->isPinned()){
            $message = "Epinglage du commentaire publié par " . $comment->getAuthor()->getUsername() . " le " . $comment->getUpdatedAt()->format("d/m/Y à H:i") . " pour la publication " . $post->getTitle();
        } else{
            $message = "Désépinglage du commentaire publié par " . $comment->getAuthor()->getUsername() . " le " . $comment->getUpdatedAt()->format("d/m/Y à H:i") . " pour la publication " . $post->getTitle();
        }
        $objects = array(
            "post" => $post,
            "comment" => $comment
        );

        return new Action($message, $objects);
    }
}