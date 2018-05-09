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
        $posts = $post_retriever->getPosts($this->get('security.token_storage')->getToken()->getUser(), null, 3); 
        $args = array(            
            'currentSection' => 'édition',
            'posts' => $posts
        );

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }
}
