<?php

namespace MesClics\PostBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MesClics\PostBundle\Entity\Collection;
use Symfony\Component\HttpFoundation\Request;
use MesClics\PostBundle\Form\MesClicsCollectionType;
use MesClics\PostBundle\Widget\CollectionsHomeWidgets;
use MesClics\PostBundle\Widget\CollectionCreationWidgets;
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

    public function __construct(EntityManagerInterface $em, CollectionFormManager $collection_form_manager, $adminAvailableCollections){
        $this->collection_form_manager = $collection_form_manager;
        $this->available_collections = $adminAvailableCollections;
    }

    public function collectionsAction(CollectionsHomeWidgets $widgets, Request $request){
        $params = array(
            'available_collections' => $this->available_collections
        );
        $widgets->initialize($params);
        // add class to collection_creation widget
        $widgets->getWidget('collection_creation')->addClass("highlight2");

        $widgets->handleRequest($request);
        $args = array(
            'navRails' => array(
                'edition' => $this->generateUrl('mesclics_admin_edition'),
                'collections' => $this->generateUrl('mesclics_admin_collections')
            ),
            'widgets' => $widgets->getWidgets()
        );

        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    public function newAction(CollectionCreationWidgets $widgets, Request $request){
        $params = array(
            'available_collections' => $this->available_collections
        );

        $widgets->initialize($params);
        // add class to widget
        $widgets->getWidget('collection_creation')->addClass('highlight');
        $widgets->handleRequest($request);

        $args = array(
            'navRails' => array(
                "edition" => $this->generateUrl('mesclics_admin_edition'),
                "collections" => $this->generateUrl('mesclics_admin_collections'),
                "nouvelle collection" => $this->generateUrl('mesclics_admin_collections_new')
            ),
            'widgets' => $widgets->getWidgets()
        );
        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
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
