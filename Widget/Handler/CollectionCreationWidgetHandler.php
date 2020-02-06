<?php

namespace MesClics\PostBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\PostBundle\Entity\Collection;
use Symfony\Component\HttpFoundation\Request;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\PostBundle\Event\MesClicsCollectionEvents;
use MesClics\PostBundle\Widget\CollectionCreationWidget;
use MesClics\PostBundle\Event\MesClicsCollectionCreationEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class CollectionCreationWidgetHandler extends WidgetHandler{

    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof CollectionCreationWidget){
            throw new InvalidArgumentException(__METHOD__ . ' expects first argument to be an instance of CollectionCreationWidget, ' . get_class($widget) . ' given.');
        }

        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

            if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $collection = new Collection();
                $dto = $widget->getForm()->getData();

                $dto->mapTo($collection);
                $this->entity_manager->persist($collection);
                //TODO: dispatch event
                $event = new MesClicsCollectionCreationEvent($collection);
                $this->event_dispatcher->dispatch(MesClicsCollectionEvents::CREATION, $event);
                $this->entity_manager->flush();
            }
        }
    }
}