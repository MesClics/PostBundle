<?php

namespace MesClics\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EditionController extends Controller{
    
    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function indexAction(){
        $post_retriever = $this->get('mesclics_post.retriever');
        //on ne récupère que les 5 derniers messages
        $post_retriever
            ->addOrderParams(array(
            'date-creation' => 'dateCreation',
            'date-publication-debut' => 'datePublication',
            'date-publication-fin' => 'datePeremption',
            'titre' => 'title'))
            ->setLimit(5)
            ->setOrderBy('date-creation')
            ->setOrder('DESC');
        
        $posts = $post_retriever->getPosts(); 
        $args = array(            
            'currentSection' => 'édition',
            'posts' => $posts
        );

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }
}
