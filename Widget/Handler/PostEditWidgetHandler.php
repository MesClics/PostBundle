<?php

namespace MesClics\PostBundle\Widget\Handler;

use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Form\Form;
use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use MesClics\UtilsBundle\Widget\Widget;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Form\DTO\PostDTO;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Widget\PostEditWidget;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\PostBundle\Event\MesClicsPostUpdateEvent;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\UtilsBundle\Widget\WidgetHandlerInterface;
use MesClics\PostBundle\Event\MesClicsPostCategorizationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PostEditWidgetHandler extends WidgetHandler{

    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof PostEditWidget){
            throw new InvalidArgumentException(__METHOD__.' expects instance of PostEditWidget as first argument, ' . get_class($widget) . ' instance given.');
        }
        
        $widget->getForm()->handleRequest($request);
        
        if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
            // make sure that the submitted form is the good one
            // if(get_class($widget->getForm()->getData()) !== PostDTO::class){
            //     return;
            // }

            $before_update = clone $widget->getPost();
            $post_dto = $widget->getForm()->getData();
            $post_dto->mapTo($widget->getPost());

            if($post_dto->getOldCollections() !== $widget->getPost()->getCollections()->toArray()){
                $post_dto->addUpdatedData('collections', $post_dto->getOldCollections(), $widget->getPost()->getCollections()->toArray());
            }

            if($post_dto->getUpdatedDatas()){
                //dispatch MesClicsPostUpdateEvent
                $event = new MesClicsPostUpdateEvent($before_update, $widget->getPost());
                $this->event_dispatcher->dispatch(MesClicsPostEvents::UPDATE, $event);

                // dispatch MesClicsPostCategorizationEvent if needed
                if($post_dto->getUpdatedData("collections")){
                    $cat_event = new MesClicsPostCategorizationEvent($post_dto->getUpdatedData("collections")[0], $post_dto->getUpdatedData("collections")[1], $widget->getPost());
                    $this->event_dispatcher->dispatch(MesClicsPostEvents::CATEGORIZATION, $cat_event);
                }
            }
            
            $this->entity_manager->flush();
        }
    }
}