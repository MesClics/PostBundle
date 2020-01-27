<?php

namespace MesClics\PostBundle\Controller;

use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Form\DTO\PostDTO;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\PostBundle\Widget\PostUpdateWidgets;
use MesClics\PostBundle\Popups\MesClicsPostPopups;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use MesClics\PostBundle\Event\MesClicsPostRemovalEvent;
use MesClics\PostBundle\Event\MesClicsPostCreationEvent;
use MesClics\PostBundle\Form\FormManager\PostFormManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostController extends Controller
{
    private $post_retriever;
    private $form_manager;
    private $token_storage;
    private $event_dispatcher;
    private $entity_manager;

    public function __construct(PostRetriever $post_retriever, PostFormManager $form_manager, TokenStorageInterface $token_storage, EventDispatcherInterface $ed, EntityManagerInterface $em){
        $this->post_retriever = $post_retriever;
        $this->form_manager = $form_manager;
        $this->token_storage = $token_storage;
        $this->event_dispatcher = $ed;
        $this->entity_manager = $em;
    }
    
    public function initializePostRetriever(Request $request){        
        //on ajoute les éventuels paramètres de tri
        //on établit d'abord la liste des éventuels paramètres de tri des résultats qu'on passera au postRetriever :
        $order_params = array(
            'date-creation' => 'dateCreation',
            'date-publication-debut' => 'datePublication',
            'date-publication-fin' => 'datePeremption',
            'titre' => 'title'
        );
        $this->post_retriever->addOrderParams($order_params);
        
        //ORDER-BY
        if($request->query->get('order-by')){
            $order_by = $request->query->get('order-by');
        } else{
            //par défaut on trie par date de création
            $order_by = 'date-creation';
        }
        $this->post_retriever->setOrderBy($order_by);

        //SORT
        if($request->query->get('sort')){
            
            $sort = $request->query->get('sort');
        } else{
            //par défaut on trie apr ordre croissant saud si le critère de tri commence par date-
            if(!preg_match('/^date-/m', $this->post_retriever->getOrderBy())){
                $sort = 'ASC';
            } else{
                $sort = 'DESC';
            }
        }
        $this->post_retriever->setOrder($sort);

        return $this->post_retriever;
    }

    /**
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function postsAction(Array $sub_args = null, Request $request){
        $args = array(
            'currentSection' => 'édition',
            'subSection' => 'posts'
        );

        //on récupère les posts
        $this->post_retriever = $this->initializePostRetriever($request);
        //on passe les critères de tri à la vue
        $args['sort_params'] = array(
            'order_by' => $this->post_retriever->getOrderBy(),
            'sort' => $this->post_retriever->getOrder()
        );

        $posts = $this->post_retriever->getPosts();
        if($posts){
            $args['posts'] = $posts;
        }

        //on ajoute les sub_args (popups si nécessaire)
        if($sub_args){
            $args = array_merge($args, $sub_args);
        }

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function newAction(Request $request){
        //on génère un formulaire pour la création d'uun nouveau post.
        $postDTO = new PostDTO($this->entity_manager);
        $post_form = $this->createForm(PostType::class, $postDTO);

        //on traite éventuellement le formulaire
        if($request->isMethod('POST')){
            $post_form->handleRequest($request);
            if($post_form->isSubmitted() &&  $post_form->isValid()){
                //map DTO to Post Entity + addAuthor
                $post = new Post();
                $postDTO->mapTo($post);
                $post->addAuthor($this->token_storage->getToken()->getUser());

                $this->entity_manager->persist($post);

                //dispatch a MesClicsPostCreationEvent :
                $event = new MesClicsPostCreationEvent($post);
                $this->event_dispatcher->dispatch(MesClicsPostEvents::CREATION, $event);

                
                $this->entity_manager->flush();

                //redirect to the post page
                $args = array(
                    'post_id' => $post->getID()
                );
                return $this->redirectToRoute("mesclics_admin_post", $args);
            }
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'posts',
            'postSection' => 'new',
            'new_post_form' => $post_form->createView(),
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
