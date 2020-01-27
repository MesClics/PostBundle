<?php

namespace MesClics\PostBundle\Popups;

class MesClicsPostBackendCommentPopups{
    public static function onRemoval(Array &$popups){
        $popups['delete'] = array(
            "options" => array(
                "illustration" => array(
                    "url" => "@mesclicscommentbundle/images/icones/remove.svg",
                    "title" => "supprimer un commentaire",
                    "alt" => "icone de confirmation de suppression de commentaire",
                    "type" => "svg",
                    "class" => "comment-remove"
                ),
                "class" => "alert"
            ),
            "template" =>  "MesClicsPostBundle:PopUps:backend_comment_removal.html.twig"
        );
    }
}