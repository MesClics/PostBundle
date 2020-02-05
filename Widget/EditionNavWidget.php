<?php

namespace MesClics\PostBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;

class EditionNavWidget extends Widget{
    public function getName(){
        return 'edition_nav';
    }

    public function getTemplate(){
        return 'MesClicsPostBundle:Widgets:edition-nav.html.twig';
    }

    public function getVariables(){
        return null;
    }
}