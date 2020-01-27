<?php

namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use MesClics\UtilsBundle\Widget\Widget;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Form\DTO\PostDTO;
use Symfony\Component\Form\FormFactoryInterface;
use MesClics\PostBundle\Widget\Handler\PostEditWidgetHandler;

class PostEditWidget extends Widget{
    private $post;
    private $form;

    public function __construct(Post $post, PostEditWidgetHandler $handler){
        $this->post = $post;
        $this->handler = $handler;
        $post_dto = new PostDTO();
        $post_dto->mapFrom($this->post);
        $this->form = $this->createForm(PostType::class, $post_dto);
    }

    public function getPost(){
        return $this->post;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return "mesclics_post_edit";
    }

    public function getTemplate(){
        return "MesClicsPostBundle:Widgets:post-update.html.twig";
    }

    public function getVariables(){
        return array(
            "post" => $this->getPost(),
            "form" => $this->getForm()->createView()
        );
    }
}