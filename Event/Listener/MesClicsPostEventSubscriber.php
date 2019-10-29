<?php
namespace MesClics\PostBundle\Event\Listener;

use Psr\Log\LoggerInterface;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\NavigationBundle\Navigator\Navigator;
use MesClics\PostBundle\Actions\MesClicsPostActions;
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
            MesClicsPostEvents::CREATION => "onCreation",
            MesClicsPostEvents::UPDATE => "onUpdate",
            MesClicsPostEvents::REMOVAL => "onRemoval",
            MesClicsPostEvents::PUBLICATION => "onPublication",
            MesClicsPostEvents::DEPUBLICATION => "onDepublication",
            MesClicsPostEvents::CATEGORIZATION => "onCategorization"
        );
    }

    public function onCreation(MesClicsPostCreationEvent $event){
        $post = $event->getPost();
        switch ($post->getVisibilite()){
            case "public":
            $visibilite = "publique";
            break;

            case "private":
            $visibilite = "privée";
            break;
        }
        // add a flash message
        $message = $this->getCreationMessage($post);

        $this->addFlash('success', $message);
        // add as action to navigator
        $action = MesClicsPostActions::creation($post);
        $this->navigator->getUser()->getChronology()->addAction($action);
    }

    public function onUpdate(MesClicsPostUpdateEvent $event){
        $beforeUpdate = $event->getBeforeUpdate();
        $afterUpdate = $event->getAfterUpdate();
        $beforeUpdateFilters = $beforeUpdate->getFilters();
        $afterUpdateFilters = $afterUpdate->getFilters();
        // dispatch publications Events
        if($beforeUpdate->getDatePublication() !== $afterUpdate->getDatePublication()){
            // publication date has changed
            if(($beforeUpdate->isOnline() || $beforeUpdate->willBePublished()) && ($afterUpdate->isOnline() || $afterUpdate->willBePublished())){
                $publicationEvent = new MesClicsPostPublicationEvent($afterUpdate);
                $publicationEventName = MesClicsPostEvents::PUBLICATION;
            }
            //publication is now online
            if(!$beforeUpdate->isOnline() && $afterUpdate->isOnline()){
                $publicationEvent = new MesClicsPostPublicationEvent($afterUpdate);
                $publicationEventName = MesClicsPostEvents::PUBLICATION;
            }
            //publication is now offline
            if($beforeUpdate->isOnline() && !$afterUpdate->isOnline()){
                $publicationEvent = new MesClicsPostDepublicationEvent($afterUpdate);
                $publicationEventName = MesClicsPostEvents::DEPUBLICATION;
            }
            //TODO: publication is now draft
        }

        if(($beforeUpdate->getDatePeremption() !== $afterUpdate->getDatePeremption())|| ($beforeUpdate->getDatePeremption() && !$afterUpdate->getDAtePeremption())){
            $publicationEvent = new MesClicsPostDepublicationEvent($afterUpdate);
            $publicationEventName = MesClicsPostEvents::DEPUBLICATION;
        }

        if($publicationEvent && $publicationEventName){
            $this->event_dispatcher->dispatch($publicationEventName, $publicationEvent);
        }

        // add a flash message for non publication dates updates
        $this->addFlash('success', 'Votre publication ' . $event->getAfterUpdate()->getTitle() . ' a bien été modifiée.');


        // add as action to navigator
        $action = MesClicsPostActions::update($afterUpdate);
        $this->navigator->getUser()->getChronology()->addAction($action);

    }

    public function onRemoval(MesClicsPostRemovalEvent $event){
        // add a flash message
        $this->addFlash('success', 'Votre publication ' . $event->getPost()->getTitle() . ' a bien été supprimée.');
        // add as action to navigator
        $action = MesClicsPostActions::removal($post);
        $this->navigator->getUser()->getChronology()->addAction($action);
    }

    public function onPublication(MesClicsPostPublicationEvent $event){
        // add a flash message
        $post = $event->getPost();
        if($post->willBePublished()){
            $message = "Votre publication " . $event->getPost()->getTitle() . " sera publiée le " . $post->getDatePublication()->format("d/m/Y à H\hi");
        }

        if($post->isOnline()){
            $message = "Votre publication " . $event->getPost()->getTitle() . " est publiée";
        }

        $message .= " en mode " . $post->getVisibilite() . ".";
        $this->addFlash('success', $message);
    }

    public function onDepublication(MesClicsPostDepublicationEvent $event){
        $post = $event->getPost();
        if($post->willBeUnpublished()){
            if($post->WillBePublished()){
                $message = "Votre publication sera mise en ligne le " . $post->getDatePublication()->format("d/m/Y à H\hi") . " et sera dépubliée le " . $post->getDatePeremption()->format("d/m/y à H\hi");
            } else{
                $message = "Votre publication sera dépubliée le " . $post->getDatePeremption()->format("d/m/Y à H\hi"); 
            }
        }

        if($post->hasBeenUnpublished()){
            $message = "Votre publication a été dépubliée";
        }

        if(!$post->willBeUnpublished() && $post->isOnline()){
            $message = "Votre publication ne sera plus dépubliée à la date initialement prévue et restera en ligne jusqu'à nouvel ordre";
        }

        if(!$post->willBeUnpublished() && $post->willBePublished()){
            $message = "Votre publication sera publiée le " . $post->getDatePublication()->format("d/m/Y à H\hi"). " et restera en ligne jusqu'à nouvel ordre";
        }

        $message .= ".";

        // add a flash message
        $this->addFlash('success', $message);
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
            $message .= "La publication " . $event->getPost()->getTitle() . " a bien été ajoutée aux collections suivantes : " . implode(", ", $categories) . '. ';
        }

        
        if($type["removed_from"]){
            $categories = array();
            foreach($event->getOldCollections() as $cat){
                $categories[] = $cat->getName();
            }
            $message .= "La publication " . $event->getPost()->getTitle() . " a bien été retirée des collections suivantes : " . implode(", ", $categories) . '. ';
        }

        $this->addFlash('success', $message);
    }

    private function getCreationMessage(Post $post){
        
        if($post->isOnline()){
            $message = "Votre nouvelle publication " . $visibilite . " a bien été publiée";
             if($post->willBeUnpublished()){
                 $message .= " et sera en ligne jusqu'au " . $post->getDatePeremption()->format('d/m/Y à H\hi');
             }
             $message .= ".";
        } else{
            $message = "Votre publication " . $visibilite . " a bien été enregistrée";
            if($post->isDraft()){
                $message .= " en tant que brouillon";
            }
            if($post->willBePublished()){
                if($post->willBeUnpublished()){
                    $publicationDates = " du " . $post->getDatePublication()->format('d/m/Y à H\hi') . " au " . $post->getDateDepublication()->format('d/m/Y à H\hi');
                } else{
                    $publicationDates = " le " . $post->getDatePublication()->format('d/m/Y à H\hi');
                }
                $message = " et sera publiée le " . $publicationDates;
            }
        }

        return $message;
    }
}