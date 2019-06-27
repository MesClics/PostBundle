<?php

namespace MesClics\PostBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Entity\Collection;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Form\MesClicsCollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\PostBundle\Form\FormManager\CollectionFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Security("has_role('ROLE_EDITOR')")
 */
class CollectionController extends Controller{    
    private $collection_form_manager;
    private $availbale_collections;
    private $repo;

    public function __construct(EntityManagerInterface $em, CollectionFormManager $collection_form_manager, $adminAvailableCollections){
        $this->collection_form_manager = $collection_form_manager;
        $this->available_collections = $adminAvailableCollections;
        $this->repo = $em->getRepository('MesClicsPostBundle:Collection');
    }

    public function collectionsAction(Request $request){
        //on récupère la liste des collections déjà existantes
        $collections = $this->repo->getCollections();

        //on ajoute un formulaire pour l'ajout de collection
        $options = array(
            "available_collections" => $this->available_collections
        );
        $collection = new Collection();
        $new_collection_form = $this->createForm(MesClicsCollectionType::class, $collection, $options);
        if($request->isMethod('POST')){
            $this->collection_form_manager->handle($new_collection_form);
            if($this->collection_form_manager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_collection', array('collection_id' => $collection->getId()));
            }
        }
        //on renvoie la vue permettant 'laffichage de la liste des collections
        $args = array(
            "currentSection" => "edition",
            "subSection" => "collections",
            "collections" => $collections
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

    public function newAction(Request $request){
        //on crée un objet collection
        $collection = new Collection();
        //on crée un formulaire
        $options['available_collections'] = $this->available_collections;

        //on récupère les collections par type d'objets
        $collections = $this->repo->getCollections();

        $form = $this->createForm(MesClicsCollectionType::class, $collection, $options);
        if($request->isMethod('POST')){
            //on initialise le form_manager
            $form_manager = $this->collection_form_manager;
            $form_manager->handle($form);
            if($form_manager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_collections');
            }
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'collections',
            'collectionSection' => 'new',
            'new_collection_form' => $form->createView(),
            'collections' => $collections
        );
        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("collection", options={"mapping": {"collection_id": "id"}})
     */
    public function updateAction(Collection $collection, Request $request){
        $options['available_collections'] = $this->available_collections;
        $form = $this->createForm(MesClicsCollectionType::class, $collection, $options);
        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'collections',
            'collectionSection' => 'edit',
            'collection' => $collection,
            'update_collection_form' => $form->createView()
        );

        if($request->isMethod('POST')){
            $this->collectionFormManager->handle($form);
            if($this->collectionFormManager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_collection', array('collection_id' => $collection->getId()));
            }
        }

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
