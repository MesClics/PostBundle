<?php

namespace MesClics\PostBundle\Controller;

use MesClics\PostBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\CommentBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Form\PostBackendCommentType;
use MesClics\PostBundle\Form\DTO\PostBackendCommentDTO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\PostBundle\Popups\MesClicsPostBackendCommentPopups;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentPinEvent;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentUpdateEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentRemovalEvent;
use MesClics\PostBundle\Event\MesClicsPostBackendCommentCreationEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BackendCommentController extends Controller{
    private $entity_manager;
    private $event_dispatcher;
    private $token_storage;

    public function __construct(EntityManagerInterface $entity_manager, EventDispatcherInterface $event_dispatcher, TokenStorageInterface $token_storage){
        $this->entity_manager = $entity_manager;
        $this->event_dispatcher = $event_dispatcher;
        $this->token_storage = $token_storage;
    }

    
    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function createAction(Post $post, Request $request){
        // TODO: check if user can post comments on this post
        $commentDTO = new PostBackendCommentDTO($this->token_storage->getToken()->getUser());

        $form = $this->createForm(PostBackendCommentType::class, $commentDTO, array(
            "action" => $this->generateUrl('mesclics_admin_post_backend_comment', array('post_id' => $post->getId()))
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() || $form->isValid()){
            $comment = new Comment();
            $commentDTO->mapTo($comment);
            if($comment->getParent()){
                $comment->getParent()->addChild($comment);
            }
            $this->entity_manager->persist($comment);
            // dispatch post backend comment event;
            $event = new MesClicsPostBackendCommentCreationEvent($comment, $post);
            $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::CREATION, $event);
            $post->addBackendComment($comment);

            $this->entity_manager->flush();
        }
        return $this->redirectToRoute("mesclics_admin_post", array("post_id" => $post->getId()));
    }

    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     * @ParamConverter("comment", options={"mapping": {"comment_id": "id"}})
     */
    public function updateAction(Post $post, Comment $comment, Request $request){
        $commentDTO = new PostBackendCommentDTO($this->token_storage->getToken()->getUser());
        $commentDTO->mapFrom($comment);

        $form = $this->createForm(PostBackendCommentType::class, $commentDTO, array(
            "action" => $this->generateUrl('mesclics_admin_post_backend_comment_update', array('comment_id' => $comment->getId(), 'post_id' => $post->getId()))
        ));

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $before_update = clone $comment;
                $form->getData()->mapTo($comment);
                $event = new MesClicsPostBackendCommentUpdateEvent($before_update, $comment, $post);
                $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::UPDATE, $event);
                $this->entity_manager->flush();

                return $this->redirectToRoute('mesclics_admin_post', array("post_id" => $post->getId()));
            }
        }
        $args = array(
            "form" => $form->createView()
        );

        $html = $this->render("MesClicsCommentBundle:Forms:comment-form.html.twig", $args);
        return $html;
    }

    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     * @ParamConverter("parent", options={"mapping": {"comment_id": "id"}})
     */
    public function replyAction(Post $post, Comment $parent, Request $request){
        // TODO: check if user can post comments on this post
        $commentDTO = new PostBackendCommentDTO($this->token_storage->getToken()->getUser());
        $commentDTO->setParent($parent);
        
        $form = $this->createForm(PostBackendCommentType::class, $commentDTO, array(
             "action" => $this->generateUrl('mesclics_admin_post_backend_comment_reply', array('post_id' => $post->getId(), "comment_id" => $parent->getId()))
        ));

        if($request->isMethod("POST")){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $comment = new Comment();
                $form->getData()->mapTo($comment);
                $this->entity_manager->persist($comment);
                $event = new MesClicsPostBackendCommentCreationEvent($comment, $post);
                $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::CREATION, $event);
                $this->entity_manager->flush();

                return $this->redirectToRoute("mesclics_admin_post", array("post_id" => $post->getId()));
            }
        }

        $args = array(
            "form" => $form->createView()
        );
        
        $html = $this->render("MesClicsCommentBundle:Forms:comment-form.html.twig", $args);
        return $html;
    }

    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     * @ParamConverter("comment", options={"mapping": {"comment_id": "id"}})
     */
    public function removeAction(Post $post, Comment $comment){
        // TODO: check if user can remove backend comments for this post
        $popups = array();
        MesClicsPostBackendCommentPopups::onRemoval($popups);

        $args = array(
            'post' => $post,
            'comment' => $comment,
            'popups' => $popups
        );

        return $this->render("MesClicsBundle:PopUps:renderer.html.twig", $args);
    }

    
    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     * @ParamConverter("comment", options={"mapping": {"comment_id": "id"}})
     */
    public function deleteAction(Post $post, Comment $comment){
        // TODO: check if user can delete backend comments for this post
        $this->entity_manager->remove($comment);

        $event = new MesClicsPostBackendCommentRemovalEvent($comment, $post);
        $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::REMOVAL, $event);
        
        $this->entity_manager->flush();

        return $this->redirectToRoute("mesclics_admin_post", array("post_id" => $post->getId()));

    }

    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     * @ParamConverter("comment", options={"mapping": {"comment_id": "id"}})
     */
    public function pinAction(Post $post, Comment $comment, Request $request){
        // TODO: add security to be sure user can see/edit/pin comment
        $comment->setIsPinned(!$comment->isPinned());
        $event = new MesClicsPostBackendCommentPinEvent($comment, $post);
        $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::PIN, $event);
        $this->entity_manager->flush();
        
        return $this->render("MesClicsPostBundle:Widgets:post-backend-comment.html.twig", array('post'=> $post, 'form'=>$this->generateAndHandleCommentForm($post, $request)->createView()));
    }

    private function generateAndHandleCommentForm(Post $post, Request $request){
        
        // TODO: check if user can post comments on this post
        $commentDTO = new PostBackendCommentDTO($this->token_storage->getToken()->getUser());

        $form = $this->createForm(PostBackendCommentType::class, $commentDTO, array(
            "action" => $this->generateUrl('mesclics_admin_post_backend_comment', array('post_id' => $post->getId()))
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() || $form->isValid()){
            $comment = new Comment();
            $commentDTO->mapTo($comment);
            if($comment->getParent()){
                $comment->getParent()->addChild($comment);
            }
            $this->entity_manager->persist($comment);
            // dispatch post backend comment event;
            $event = new MesClicsPostBackendCommentCreationEvent($comment, $post);
            $this->event_dispatcher->dispatch(MesClicsPostBackendCommentEvents::CREATION, $event);
            $post->addBackendComment($comment);

            $this->entity_manager->flush();
            return $this->redirectToRoute("mesclics_admin_post", array("post_id" => $post->getId()));
        }
        return $form;
    }
}