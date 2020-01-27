<?php


namespace MesClics\PostBundle\Widget\Handler;


use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Form\Form;
use MesClics\PostBundle\Entity\Post;
use MesClics\UtilsBundle\Widget\Widget;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\CommentBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\PostBundle\Form\DTO\PostBackendCommentDTO;
use MesClics\UtilsBundle\Widget\WidgetHandlerInterface;
use MesClics\PostBundle\Widget\PostBackendCommentsWidget;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentCreationEvent;

class PostBackendCommentsWidgetHandler extends WidgetHandler{
    
    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof PostBackendCommentsWidget){
            throw new InvalidArgumentException(__METHOD__.' expects instance of PostBackendCommentsWidget as first argument');
        }

        // if(get_class($widget->getForm()->getData()) !== PostBackendCommentDTO::class){
        //     return;
        // }

        $widget->getForm()->handleRequest($request);

        if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
            $comment = new Comment();
            $commentDTO = $widget->getForm()->getData();
            $commentDTO->mapTo($comment);
            if($comment->getParent()){
                $comment->getParent()->addChild($comment);
            }
            $this->entity_manager->persist($comment);
            // dispatch post backend comment event;
            $event = new MesClicsPostBackendCommentCreationEvent($comment, $widget->getPost());
            $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::CREATION, $event);
            $widget->getPost()->addBackendComment($comment);

            $this->entity_manager->flush();
        }

    }
}