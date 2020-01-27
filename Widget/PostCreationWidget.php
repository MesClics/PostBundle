<?php
namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use MesClics\UtilsBundle\Widget\Widget;
use MesClicsBundle\Entity\MesClicsUser;
use MesClics\PostBundle\Form\DTO\PostDTO;
use MesClics\PostBundle\Widget\Handler\PostCreationWidgetHandler;

class PostCreationWidget extends Widget{
    protected $form;
    protected $author;
    protected $post;

    public function __construct(MesClicsUser $author, PostCreationWidgetHandler $handler){
        $this->author = $author;
        $this->handler = $handler;

        $postDTO = new PostDTO();
        $this->form = $this->createForm(PostType::class, $postDTO);
    }

    public function getName(){
        return 'post_creation';
    }

    public function getTemplate(){
        return 'MesClicsPostBundle:Widgets:post-new.html.twig';
    }

    public function getVariables(){
        return array(
            'form' => $this->getForm()->createView()
        );
    }

    public function getForm(){
        return $this->form;
    }

    public function getAuthor(){
        return $this->author;
    }

    public function setPost(Post $post){
        $this->post = $post;
    }

    public function getPost(){
        return $this->post;
    }
}