<?php
namespace MesClics\PostBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;

class PostFormManager extends FormManager{
    const ERROR_NOTIFICATION_SINGULIER = "La publication n'a pas pu être créée. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "La publication a bien été créée.";

    // public function handle(Form $form, $addNotification = true){
    //     $this->hydrate(array(
    //         'form' => $form
    //     ));

    //     $this->getForm()->handleRequest($this->getRequest());

    //     if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
    //         $this->setAction($this->getForm()->getClickedButton()->getName());
    //         //on récupère les éventuelles nouvelles collections:
    //         $collections = $this->getForm()->get('collections_add')->getData();
    //         foreach($collections as $collection){
    //             $new_collec = new Collection('post');
    //             $new_collec->setName($collection['name']);
    //             $new_collec->setDescription($collection['description']);
    //             $this->getEm()->persist($new_collec);
    //         }
    //         $this->getEm()->flush();
    //         $object = $this->getForm()->getData();
    //         //on persiste notre objet en bdd

    //         $this->getEm()->persist($object);
    //         $this->getEm()->flush();
    //         $this->setResult($object);
    //         $result_count = $this->getResultCount();
    //         $this->setResultCount($result_count++);
    //         $this->setSuccess(true);
    //     }
    //     if($addNotification){
    //         $this->setNotification();
    //     }
    //     return $this;
    // }
}