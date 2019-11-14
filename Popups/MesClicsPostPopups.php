<?php
namespace MesClics\PostBundle\Popups;

class MesClicsPostPopups{
    public static function onDelete(Array &$popups){
        $popups['delete'] = array(
            'options' => array(
                'illustration' => array(
                    'url' => '@mesclicspostbundle/images/icones/publication-remove.svg',
                    'alt' => 'illustration de suppression de publication',
                    'title' => 'supprimer une publication',
                    'type' => 'svg',
                    'class' => 'post-remove'
                ),
                'class' => 'alert'
            ),
            'template' => 'MesClicsPostBundle:PopUps:post-delete-confirm.html.twig'
        );
    }
    
}