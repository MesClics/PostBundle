<?php

namespace MesClics\PostBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Widget\PostsListWidget;
use Symfony\Component\Form\FormFactoryInterface;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;

class PostsListWidgetHandler extends WidgetHandler{
    protected $post_retriever;
    
    public function __construct(FormFactoryInterface $form_factory, EntityManagerInterface $entity_manager, EventDispatcherInterface $event_dispatcher, PostRetriever $post_retriever){
        parent::__construct($form_factory, $entity_manager, $event_dispatcher);
        $this->post_retriever = $post_retriever;
    }

    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof PostsListWidget){
            throw new InvalidArgumentException(__METHOD__.' expects instance of PostsListWidget as first argument, ' . get_class($widget) . ' instance given.');
        }
        //on ajoute les éventuels paramètres de tri
        //on établit d'abord la liste des éventuels paramètres de tri des résultats qu'on passera au postRetriever :
        $order_params = array(
            'date-creation' => 'dateCreation',
            'date-publication-debut' => 'datePublication',
            'date-publication-fin' => 'datePeremption',
            'titre' => 'title'
        );
        $widget->getPostRetriever()->addOrderParams($order_params);
        
        //ORDER-BY
        if($request->query->get('order-by')){
            $order_by = $request->query->get('order-by');
        } else{
            //par défaut on trie par date de création
            $order_by = 'date-creation';
        }
        $widget->getPostRetriever()->setOrderBy($order_by);

        //SORT
        if($request->query->get('sort')){
            
            $sort = $request->query->get('sort');
        } else{
            //par défaut on trie apr ordre croissant saud si le critère de tri commence par date-
            if(!preg_match('/^date-/m', $widget->getPostRetriever()->getOrderBy())){
                $sort = 'ASC';
            } else{
                $sort = 'DESC';
            }
        }
        $widget->getPostRetriever()->setOrder($sort);
    }

    public function getPostRetriever(){
        return $this->post_retriever;
    }
}