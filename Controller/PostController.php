<?php

namespace MesClics\PostBundle\Controller;

use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use MesClics\PostBundle\Form\FormManager\PostFormManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
            // var_dump($sort);
        } else{
            //par défaut on trie apr ordre croissant saud si le critère de tri commence par date-
            if(!preg_match('/^date-/m', $this->post_retriever->getOrderBy())){
                $sort = 'ASC';
            } else{
                $sort = 'DESC';
            }
        }
        $this->post_retriever->setOrder($sort);

        //FILTER
        if($request->query->get('filter')){
            $filter = $request->query->get('filter');
        } else{
            $filter = null;
        }
        $this->post_retriever->setFilter($filter);

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
            'filter' => $this->post_retriever->getFilter(),
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
    public function newAction(Request $request){
        //on génère un formulaire pour la création d'uun nouveau post.
        $post = new Post();
        $post->addAuthor($this->token_storage->getToken()->getUser());
        $post_form = $this->createForm(PostType::class, $post);

        //on traite éventuellement le formulaire
        if($request->isMethod('POST')){
            $this->form_manager->handle($post_form);
            if($this->form_manager->hasSucceeded()){
                $args = array(
                    'post_id' => $this->form_manager->getResult()->getID()
                );
                return $this->redirectToRoute("mesclics_admin_post", $args);
            }
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'posts',
            'postSection' => 'new',
            'new_post_form' => $post_form->createView()
        );

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    /**
     * @ParamConverter("post", options={"mapping":{"post_id": "id"}})
     * @Security("has_role('ROLE_WRITER')")
     */
    public function editAction(Post $post, Request $request){
        //on vérifie que l'utilisateur courant fasse bien partie des auteurs de la publication
        $user = $this->token_storage->getToken()->getUser();
        if(!$post->getAuthors()->contains($user)){
            throw new AccessDeniedException('Seuls les auteurs de la publication peuvent la modifier');
        }
        //on crée un formulaire avec le post courant comme ref
        $form = $this->createForm(PostType::class, $post);
        
        //on traite éventuellement le formulaire si la requête est de type post
        if($request->isMethod('POST')){
            $this->form_manager->handle($form);
            if($this->form_manager->hasSucceeded()){
                $args['post_id'] = $form_manager->getResult()->getID();
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
}
