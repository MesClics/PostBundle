<?php
namespace MesClics\PostBundle\Event\Listener;

use Psr\Log\LoggerInterface;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\PostBundle\Event\MesClicsPostUpdateEvent;
use MesClics\PostBundle\Event\MesClicsPostRemovalEvent;
use MesClics\PostBundle\Event\MesClicsPostCreationEvent;
use MesClics\PostBundle\Event\MesClicsPostPublicationEvent;
use MesClics\PostBundle\Event\MesClicsPostDepublicationEvent;
use MesClics\PostBundle\Event\MesClicsPostCategorizationEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MesClicsPostEventSubscriber implements  EventSubscriberInterface{
    public $logger;
    protected $session;
    protected $event_dispatcher;

    public function __construct(LoggerInterface $logger, SessionInterface $session, EventDispatcherInterface $ed){
        $this->logger = $logger;
        $this->session = $session;
        $this->event_dispatcher = $ed;
    }

    public function addFlash(string $label, string $message){
            $this->session->getFlashBag()->add($label, $message);
    }


    public static function getSubscribedEvents(){
        return array(
            MesClicsPostEvents::CREATION => "onCreation",
            MesClicsPostEvents::UPDATE => "onUpdate",
            MesClicsPostEvents::REMOVAL => "onRemoval",
            MesClicsPostEvents::PUBLICATION => "onPublication",
            MesClicsPostEvents::DEPUBLICATION => "onDepublication",
            MesClicsPostEvents::CATEGORIZATION => "onCategorization"
        );
    }

    public function onCreation(MesClicsPostCreationEvent $event){
        // add a flash message
        $this->addFlash('success', 'Votre nouvelle publication a bien été ajoutée.');
    }

    public function onUpdate(MesClicsPostUpdateEvent $event){
        // add a flash message
        $this->addFlash('success', 'Votre publication ' . $event->getAfterUpdate()->getTitle() . ' a bien été modifiée.');
    }

    public function onRemoval(MesClicsPostRemovalEvent $event){
        // add a flash message
        $this->addFlash('success', 'Votre publication ' . $event->getPost()->getTitle() . 'a bien été supprimée.');
    }

    public function onPublication(MesClicsPostPublicationEvent $event){
        // add a flash message
        $this->addFlash('success', 'Votre publication est publiée.');
    }

    public function onDepublication(MesClicsPostDepublicationEvent $event){
        // add a flash message
        $this->addFlash('success', 'Votre publication est dépubliée.');
    }

    public function onCategorization(MesClicsPostCategorizationEvent $event){
        // add a flash message
        $type = $event->getCategorizationType();
        $message = "";
        
        if($type["assigned_to"]){
            $categories = array();
            foreach($event->getNewCollections() as $cat){
                $categories[] = $cat->getName();
            }
            $message .= "La publication " . $event->getAfterUpdate()->getTitle() . " a bien été ajoutée aux collections suivantes : " . implode(", ", $categories) . '. ';
        }

        
        if($type["removed_from"]){
            $categories = array();
            foreach($event->getOldCollections() as $cat){
                $categories[] = $cat->getName();
            }
            $message .= "La publication " . $event->getAfterUpdate()->getTitle() . " a bien été retirée des collections suivantes : " . implode(", ", $categories) . '. ';
        }

        $this->addFlash('success', $message);
    }
}