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
    private $available_collections;

    public function __construct($adminAvailableCollections){
        $this->available_collections = $adminAvailableCollections;
    }
    
    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function indexAction(Request $request){
        TODO://add a welcoming page for Edition section with : pinned posts widget, pinned posts-collections widget, statistic widget etc...
    }
}
