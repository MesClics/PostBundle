<?php

namespace MesClics\PostBundle\Controller;

use MesClics\PostBundle\Entity\Collection;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Form\MesClicsCollectionType;
use MesClics\PostBundle\PostRetriever\PostRetriever;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\PostBundle\Form\FormManager\CollectionFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EditionController extends Controller{
    private $post_retriever;
    private $collection_form_manager;
    private $available_collections;

    public function __construct(PostRetriever $post_retriever, CollectionFormManager $collection_form_manager, $adminAvailableCollections){
        $this->post_retriever = $post_retriever;
        $this->collection_form_manager = $collection_form_manager;
        $this->available_collections = $adminAvailableCollections;
    }
    
    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function indexAction(Request $request){
        //on ne récupère que les 30 derniers posts
        $this->post_retriever
            ->addOrderParams(array(
            'date-creation' => 'dateCreation',
            'date-publication-debut' => 'datePublication',
            'date-publication-fin' => 'datePeremption',
            'titre' => 'title'))
            ->setLimit($this->post_retriever->getLimit())
            ->setOrderBy('date-creation')
            ->setOrder('DESC');
        
        $posts = $this->post_retriever->getPosts(); 
        $args = array(            
            'currentSection' => 'édition',
            'posts' => $posts
        );

        //add a form to quickly add a new collection
        $collection = new Collection();
        $options['available_collections'] = $this->available_collections;
        $collection_form = $this->createForm(MesClicsCollectionType::class, $collection, $options);
        $this->collection_form_manager->handle($collection_form);

        if($request->isMethod('POST')){
            if($this->collection_form_manager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_collection', array('collection_id' => $collection->getId()));            }
        }

        $args['collection_form'] = $collection_form->createView();

        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }
}
