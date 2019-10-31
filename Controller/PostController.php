<?php

namespace MesClics\PostBundle\Controller;

use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Form\DTO\PostDTO;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Event\MesClicsPostEvents;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use MesClics\PostBundle\Event\MesClicsPostUpdateEvent;
use MesClics\PostBundle\Event\MesClicsPostCreationEvent;
use MesClics\PostBundle\Form\FormManager\PostFormManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\PostBundle\Event\MesClicsPostCategorizationEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostController extends Controller
{
    private $post_retriever;
    private $form_manager;
    private $token_storage;

    public function __construct(PostRetriever $post_retriever, PostFormManager $form_manager, TokenStorageInterface $token_storage){
        $this->post_retriever = $post_retriever;
        $this->form_manager = $form_manager;
        $this->token_storage = $token_storage;
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
    public function postsAction(Request $request){
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

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function newAction(Request $request, EventDispatcherInterface $ed, EntityManagerInterface $em){
        //on génère un formulaire pour la création d'uun nouveau post.
        $postDTO = new PostDTO($em);
        $post_form = $this->createForm(PostType::class, $postDTO);

        //on traite éventuellement le formulaire
        if($request->isMethod('POST')){
            $post_form->handleRequest($request);
            if($post_form->isSubmitted() &&  $post_form->isValid()){
                //map DTO to Post Entity + addAuthor
                $post = new Post();
                $postDTO->mapTo($post);
                $post->addAuthor($this->token_storage->getToken()->getUser());

                $em->persist($post);

                //dispatch a MesClicsPostCreationEvent :
                $event = new MesClicsPostCreationEvent($post);
                $ed->dispatch(MesClicsPostEvents::CREATION, $event);

                
                $em->flush();

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
    public function updateAction(Post $post, Request $request, EventDispatcherInterface $ed, EntityManagerInterface $em){
        //on vérifie que l'utilisateur courant fasse bien partie des auteurs de la publication
        $user = $this->token_storage->getToken()->getUser();
        if(!$post->getAuthors()->contains($user)){
            throw new AccessDeniedException('Seuls les auteurs de la publication peuvent la modifier');
        }
        //on crée un formulaire avec le post courant comme ref
        $postDTO = new PostDTO($em);
        $postDTO->mapFrom($post);

        $form = $this->createForm(PostType::class, $postDTO);
        
        //on traite éventuellement le formulaire si la requête est de type post
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $before_update = clone $post;
                $post_dto = $form->getData();
                $post_dto->mapTo($post);

                if($post_dto->getOldCollections() !== $post->getCollections()->toArray()){
                    $post_dto->addUpdatedData('collections', $post_dto->getOldCollections(), $post->getCollections()->toArray());
                }

                if($post_dto->getUpdatedDatas()){
                    //dispatch MesClicsPostUpdateEvent
                    $event = new MesClicsPostUpdateEvent($before_update, $post);
                    $ed->dispatch(MesClicsPostEvents::UPDATE, $event);

                    // dispatch MesClicsPostCategorizationEvent if needed
                    if($post_dto->getUpdatedData("collections")){
                        $cat_event = new MesClicsPostCategorizationEvent($post_dto->getUpdatedData("collections")[0], $post_dto->getUpdatedData("collections")[1], $post);
                        $ed->dispatch(MesClicsPostEvents::CATEGORIZATION, $cat_event);
                    }
                }
                
                $em->flush();

                $args['post_id'] = $post->getID();
                return $this->redirectToRoute("mesclics_admin_post", $args);
            }
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'posts',
            'postSection' => 'edit',
            'edit_post_form' => $form->createView(),
            'currentPost' => $post
        );

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    public function removeAction(Post $post){

    }
}
