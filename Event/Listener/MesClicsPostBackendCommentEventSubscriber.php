<?php

namespace MesClics\PostBundle\Event\Listener;

use MesClics\NavigationBundle\Navigator\Navigator;
use MesClics\UtilsBundle\Functions\MesClicsFunctions;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentPinEvent;
use MesClics\PostBundle\Actions\MesClicsPostBackendCommentActions;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentUpdateEvent;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentRemovalEvent;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentCreationEvent;

class MesClicsPostBackendCommentEventSubscriber implements EventSubscriberInterface{
    private $navigator;
    private $session;

    public function __construct(Navigator $navigator, SessionInterface $session){
        $this->navigator = $navigator;
        $this->session = $session;
    }

    public static function getSubscribedEvents(){
        return array(
            MesClicsPostBackendCommentEvents::CREATION => 'onCreation',
            MesClicsPostBackendCommentEvents::UPDATE => 'onUpdate',
            MesClicsPostBackendCommentEvents::REMOVAL => 'onRemoval',
            MesClicsPostBackendCommentEvents::PIN => 'onPin'
        );
    }

    public function onRemoval(MesClicsPostBackendCommentRemovalEvent $event){
        $label = "success";
        $message = "Le commentaire d'édition pour la publication " . $event->getPost()->getTitle() . " a bien été supprimé.";

        MesClicsFunctions::addFlash($label, $message, $this->session);

        $action = MesClicsPostBackendCommentActions::onRemoval($event->getComment(), $event->getPost());
        $this->navigator->addAction($action);
    }

    public function onCreation(MesClicsPostBackendCommentCreationEvent $event){
        // TODO : send a mail/admin panel notification to the other post authors
        $label = "success";
        $message = "Votre commentaire d'édition a bien été publié.";

        if($event->getComment()->getParent()){
            $message = "Votre commentaire d'éditon en réponse au commentaire envoyé par " . $event->getComment()->getParent()->getAuthor()->getUsername() . " a bien été publié.";
        }
        
        MesClicsFunctions::addFlash($label, $message, $this->session);

        $action = MesClicsPostBackendCommentActions::onCreation($event->getComment(), $event->getPost());
        $this->navigator->addAction($action);
    }

    public function onUpdate(MesClicsPostBackendCommentUpdateEvent $event){
        $label = "success";
        $message = "Votre commentaire d'édition a bien été modifié.";

        MesClicsFunctions::addFlash($label, $message, $this->session);

        $action = MesClicsPostBackendCommentActions::onUpdate($event->getBeforeUpdate(), $event->getPost());
        $this->navigator->addAction($action);
    }

    public function onPin(MesClicsPostBackendCommentPinEvent $event){
            $label = "success";
            
        if($event->getComment()->isPinned()){
            $message = "Votre commentaire d'édtion a bien été épinglé.";
        } else{
            $message = "Votre commentaire d'édtion a bien été désépinglé.";
        }

        MesClicsFunctions::addFlash($label, $message, $this->session);

        $action = MesClicsPostBackendCommentActions::onPin($event->getComment(), $event->getPost());
        $this->navigator->addAction($action);
    }
}