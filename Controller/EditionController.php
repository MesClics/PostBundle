<?php

namespace MesClics\PostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EditionController extends Controller{
    private $post_retriever;

    public function __construct(PostRetriever $post_retriever){
        $this->post_retriever = $post_retriever;
    }
    
    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function indexAction(){
        //on ne récupère que les 30 derniers posts
        $this->post_retriever
            ->addOrderParams(array(
            'date-creation' => 'dateCreation',
            'date-publication-debut' => 'datePublication',
            'date-publication-fin' => 'datePeremption',
            'titre' => 'title'))
            ->setLimit($this->post_retriever->getLimit())
            ->setOrderBy('date-creation')
            ->setOrder('DESC');
        
        $posts = $this->post_retriever->getPosts(); 
        $args = array(            
            'currentSection' => 'édition',
            'posts' => $posts
        );

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }
}
