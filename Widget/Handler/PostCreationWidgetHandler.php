<?php
namespace MesClics\PostBundle\Widget\Handler;

use MesClics\PostBundle\Entity\Post;
use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\PostBundle\Widget\PostCreationWidget;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\PostBundle\Event\MesClicsPostCreationEvent;

class PostCreationWidgetHandler extends WidgetHandler{
    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof PostCreationWidget){
            throw new InvalidArgumentException(__METHOD__ . " expects first argument to be an instance of PostCreationWidget, instance of " . get_class($widget) . " given.");
        }

        $widget->getForm()->handleRequest($request);

        if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
            //map DTO to Post Entity + addAuthor
            $post = new Post();
            $postDTO = $widget->getForm()->getData();

            $postDTO->mapTo($post);
            $post->addAuthor($widget->getAuthor());

            $this->entity_manager->persist($post);

            //dispatch a MesClicsPostCreationEvent :
            $event = new MesClicsPostCreationEvent($post);
            $this->event_dispatcher->dispatch(MesClicsPostEvents::CREATION, $event);
            $this->entity_manager->flush();

            $widget->setPost($post);
        }
    }
}