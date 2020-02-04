<?php

namespace MesClics\PostBundle\Controller;

use MesClics\PostBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Widget\PostsHomeWidgets;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\PostBundle\Widget\PostUpdateWidgets;
use MesClics\PostBundle\Popups\MesClicsPostPopups;
use MesClics\PostBundle\Widget\PostCreationWidgets;
use MesClics\PostBundle\Event\MesClicsPostRemovalEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostController extends Controller
{
    private $token_storage;
    private $event_dispatcher;
    private $entity_manager;

    public function __construct(TokenStorageInterface $token_storage, EventDispatcherInterface $ed, EntityManagerInterface $em){
        $this->token_storage = $token_storage;
        $this->event_dispatcher = $ed;
        $this->entity_manager = $em;
    }

    /**
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function postsAction(PostsHomeWidgets $widgets_container, Array $sub_args = null, Request $request){
        $widgets_container->initialize();
        $widgets_container->handleRequest($request);

        $args = array(
            'currentSection' => 'édition',
            'subSection' => 'posts',
            'widgets' => $widgets_container->getWidgets()
        );

        //on ajoute les sub_args (popups si nécessaire)
        if($sub_args){
            $args = array_merge($args, $sub_args);
        }

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function newAction(PostCreationWidgets $widgets_container, Request $request){
        $params = array(
            'author' => $this->token_storage->getToken()->getUser()
        );
        $widgets_container->initialize($params);

        //on traite éventuellement le formulaire
        if($request->isMethod('POST')){
            $widgets_container->handleRequest($request);
            //redirect to the post page
            $args = array(
                'post_id' => $widgets_container->getWidget('post_creation')->getPost()->getID()
            );
            return $this->redirectToRoute("mesclics_admin_post", $args);
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'posts',
            'postSection' => 'edit',
            'widgets' => $widgets_container->getWidgets()
        );
        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    /**
     * @ParamConverter("post", options={"mapping":{"post_id": "id"}})
     * @Security("has_role('ROLE_WRITER')")
     */
    public function updateAction(Post $post, array $sub_args = null, PostUpdateWidgets $widgets_container, Request $request){
        //on vérifie que l'utilisateur courant fasse bien partie des auteurs de la publication
        $user = $this->token_storage->getToken()->getUser();
        if(!$post->getAuthors()->contains($user)){
            throw new AccessDeniedException('Seuls les auteurs de la publication peuvent la modifier');
        }

        //sets the $post and the $user widgets_container attrs and initialize widgets if needed;
        $widgets_vars = array(
            'post' => $post,
            'user' => $user
        );
        $widgets_container->initialize($widgets_vars);

        // handleRequest
        if($request->isMethod('POST')){
            $widgets_container->handleRequest($request);
            $args['post_id'] = $post->getID();
            return $this->redirectToRoute("mesclics_admin_post", $args);
        }

        //Set template args
        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'posts',
            'postSection' => 'edit',
            'currentPost' => $post,
            'widgets' => $widgets_container->getWidgets()
        );
        //on ajoute les sub_args (popups si nécessaire)
        if($sub_args){
            $args = array_merge($args, $sub_args);
        }

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }


    /**
     * @ParamConverter("post", options={"mapping":{"post_id": "id"}})
     */
    public function deleteAction(Post $post){
        // fire error if user is not among the authors
        if(!$post->getAuthors()->contains($this->getUser())){
            return new AccessDeniedHttpException("Désolé, seuls les auteurs d'une publication peuvent la supprimer!");
        }

        $this->entity_manager->remove($post);
        $event = new MesClicsPostRemovalEvent($post);
        $this->event_dispatcher->dispatch(MesClicsPostEvents::REMOVAL, $event);

        $this->entity_manager->flush();
        return $this->redirectToRoute("mesclics_admin_posts");
    }

    /**
     * @ParamConverter("post", options={"mapping":{"post_id": "id"}})
     */
    public function removeAction(Post $post){
        $popups = array();
        MesClicsPostPopups::onDelete($popups);
        $args = array(
            "popups" => $popups,
            "post" => $post
        );

        $popup = $this->render("MesClicsBundle:PopUps:renderer.html.twig", $args);
        return $popup;
    }
}
