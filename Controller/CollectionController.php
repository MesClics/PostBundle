<?php

namespace MC\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CollectionController extends Controller{
    
    /**
     * @Security("has_role('ROLE_WRITER')")
     */
    public function collectionsAction(){
    }

    /**
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction(){}
}
