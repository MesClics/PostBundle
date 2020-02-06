<?php
namespace MesClics\PostBundle\Event\Listener;

use Psr\Log\LoggerInterface;
use MesClics\NavigationBundle\Navigator\Navigator;
use MesClics\PostBundle\Event\MesClicsCollectionEvents;
use MesClics\PostBundle\Actions\MesClicsCollectionActions;
use MesClics\PostBundle\Event\MesClicsCollectionCreationEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MesClicsCollectionEventSubscriber implements  EventSubscriberInterface{
    public $logger;
    protected $session;
    protected $event_dispatcher;
    protected $navigator;

    public function __construct(LoggerInterface $logger, SessionInterface $session, EventDispatcherInterface $ed, Navigator $navigator){
        $this->logger = $logger;
        $this->session = $session;
        $this->event_dispatcher = $ed;
        $this->navigator = $navigator;
    }

    public function addFlash(string $label, string $message){
            $this->session->getFlashBag()->add($label, $message);
    }

    public static function getSubscribedEvents(){
        return array(
            MesClicsCollectionEvents::CREATION => 'onCreation',
            MesClicsCollectionEvents::UPDATE => 'onUpdate',
            MesClicsCollectionEvents::REMOVAL => 'onRemoval'
        );
    }

    public function onCreation(MesClicsCollectionCreationEvent $event){
        $label = "success";
        $message = "La collection d'objets de type \"" . $event->getCollection()->getEntity() . "\" nommée " . $event->getCollection()->getName() . " a bien été créée.";
        $this->addFlash($label, $message);

        $action = MesClicsCollectionActions::creation($event->getCollection());
        $this->navigator->addAction($action);
    }
}