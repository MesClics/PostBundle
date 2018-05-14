<?php

namespace MesClics\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\PostBundle\Entity\Collection;
use MesClics\PostBundle\Form\MesClicsCollectionType;

class CollectionController extends Controller{
    
    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function collectionsAction(){
        //on récupère la liste des collections déjà existantes
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('MesClicsPostBundle:Collection');

        $collections = $repo->getAll();

        //on ajoute un formulaire pour l'ajout de collection
        //on renvoie la vue permettant 'laffichage de la liste des collections
        $args = array(
            "currentSection" => "edition",
            "subSection" => "collections",
            "collections" => $collections
        );
        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction(Request $request){
        //on crée un objet collection
        $collection = new Collection();
        //on crée un formulaire
        $form = $this->createForm(MesClicsCollectionType::class, $collection);
        if($request->isMethod('POST')){
            //on initialise le form_manager
            $form_manager = $this->get('mesclics_collection.form_manager.new');
            $form_manager->handle($form);
            if($form_manager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_collections');
            }
        }

        $args = array(
            'currentSection' => 'edition',
            'subSection' => 'collections',
            'collectionSection' => 'new',
            'new_collection_form' => $form->createView()
        );
        return $this->render('MesClicsAdminBundle:Panel:edition.html.twig', $args);
    }
}
