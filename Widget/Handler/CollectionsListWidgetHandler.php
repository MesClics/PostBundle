<?php

namespace MesClics\PostBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Widget\CollectionsListWidget;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;

class CollectionsListWidgetHandler extends WidgetHandler{
    private $collections;

    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof CollectionsListWidget){
            throw new InvalidArgumentException(__METHOD__ . ' expects first argument to be an instance of CollectionsListWidget, ' . get_class($widget) . ' given.');
        }

        if($request->isMethod('POST')){
            // TODO: handle form
        }
    }

    public function getCollections(){
        if(!$this->collections){
            $repo = $this->entity_manager->getRepository("MesClicsPostBundle:Collection");
            $this->collections = $repo->getCollections();
        }
        return $this->collections;
    }
}