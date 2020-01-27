<?php

namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Entity\Post;
use MesClics\UtilsBundle\Widget\Widget;
use MesClicsBundle\Entity\MesClicsUser;
use Symfony\Component\Form\FormFactoryInterface;
use MesClics\PostBundle\Form\PostBackendCommentType;
use MesClics\PostBundle\Form\DTO\PostBackendCommentDTO;
use MesClics\PostBundle\Widget\Handler\PostBackendCommentsWidgetHandler;

class PostBackendCommentsWidget extends Widget{
    private $post;
    private $form;

    public function __construct(Post $post, MesClicsUser $author, PostBackendCommentsWidgetHandler $handler){
        $this->post = $post;
        $this->handler = $handler;
        $comment_dto = new PostBackendCommentDTO($author);
        $this->form = $this->createForm(PostBackendCommentType::class, $comment_dto);
    }

    public function getPost(){
        return $this->post;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return "mesclics_post_backend_comments";
    }

    public function getTemplate(){
        return "MesClicsPostBundle:Widgets:post-backend-comment.html.twig";
    }

    public function getVariables(){
        return array(
            "post" => $this->getPost(),
            "form" => $this->getForm()->createView()
        );
    }
}