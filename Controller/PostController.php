<?php

namespace MesClics\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Entity\Post;
use MesClics\PostBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostController extends Controller
{
    public function initializePostRetriever(Request $request){
        //on récupère les posts
        $post_retriever = $this
            ->get('mesclics_post.retriever');
        
        //on ajoute les éventuels paramètres de tri
        //on établit d'abord la liste des éventuels paramètres de tri des résultats qu'on passera au postRetriever :
        $order_params = array(
            'date-creation' => 'dateCreation',
            'date-publication-debut' => 'datePublication',
            'date-publication-fin' => 'datePeremption',
            'titre' => 'title'
        );
        $post_retriever->addOrderParams($order_params);
        
        //ORDER-BY
        if($request->query->get('order-by')){
            $order_by = $request->query->get('order-by');
        } else{
            //par défaut on trie par date de création
            $order_by = 'date-creation';
        }
        $post_retriever->setOrderBy($order_by);

        //SORT
        if($request->query->get('sort')){
            $sort = $request->query->get('sort');
            // var_dump($sort);
        } else{
            //par défaut on trie apr ordre croissant
            $sort = 'ASC';
        }
        $post_retriever->setOrder($sort);

        //FILTER
        if($request->query->get('filter')){
            $filter = $request->query->get('filter');
        } else{
            $filter = null;
        }
        $post_retriever->setFilter($filter);

        return $post_retriever;
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
        $post_retriever = $this->initializePostRetriever($request);
        //on passe les critères de tri à la vue
        $args['sort'] = $post_retriever->getOrder();
        $args['filter'] = $post_retriever->getFilter();
        $args['order_by'] = $post_retriever->getOrderBy();

        $posts = $post_retriever->getPosts();
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
        $post->addAuthor($this->get('security.token_storage')->getToken()->getUser());
        $post_form = $this->createForm(PostType::class, $post);

        //on traite éventuellement le formulaire
        if($request->isMethod('POST')){
            $post_form_manager = $this->get('mesclics_post.form_manager.new');
            $post_form_manager->handle($post_form);
            if($post_form_manager->hasSucceeded()){
                $args = array(
                    'post_id' => $post_form_manager->getResult()->getID()
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
        //on vérifie que l'utilisateur courant fasse bien aprtie des auteurs de la publication
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$post->getAuthors()->contains($user)){
            throw new AccessDeniedException('Seuls les auteurs de la publication peuvent la modifier');
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'posts',
            'postSection' => 'edit',
            'currentPost' => $post
        );

        //on crée un formulaire avec le post courant comme ref
        $form = $this->createForm(PostType::class, $post);

        $args['edit_post_form'] = $form->createView();
        
        //on traite éventuellement le formulaire si la requête est de type post
        if($request->isMethod('POST')){
            $form_manager = $this->get('mesclics_post.form_manager.new');
            $form_manager->handle($form);
            if($form_manager->hasSucceeded()){
                $args['post_id'] = $form_manager->getResult()->getID();
                $this->redirectToRoute("mesclics_admin_post", $args);
            }
        }
        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }
}
