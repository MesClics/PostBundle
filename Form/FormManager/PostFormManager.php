<?php
namespace MesClics\PostBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;
use Symfony\Component\Form\Form;
use MesClics\PostBundle\Entity\Collection;

class PostFormManager extends FormManager{
    const ERROR_NOTIFICATION_SINGULIER = "La publication n'a pas pu être créée. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "La publication a bien été créée.";

    public function handle(Form $form, $addNotification = true){
        $this->hydrate(array(
            'form' => $form
        ));

        $this->getForm()->handleRequest($this->getRequest());

        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $this->setAction($this->getForm()->getClickedButton()->getName());
            //on récupère notre objet Post
            $object = $this->getForm()->getData();
            //on récupère les éventuelles nouvelles collections:
            $collections = $this->getForm()->get('collections_add')->getData();
            if($collections){
                //pour chaque nouvelle collection
                foreach($collections as $collection){
                    //on crée un nvl objet Collection dont l'attribut entité est défini à 'post'
                    $new_collec = new Collection('post');
                    //auquel on transmet les infos name et description du formulaire
                    $new_collec->setName($collection->getName());
                    $new_collec->setDescription($collection->getDescription());
                    //on persiste notre objet
                    $this->getEm()->persist($new_collec);
                    //on ajoute la nouvelle collection à notre objet post
                    $object->addCollection($new_collec);
                }
            }
            //on envoie les modifs à la bdd
            //on persiste notre objet
            $this->getEm()->persist($object);
            $this->getEm()->flush();
            $this->setResult($object);
            $result_count = $this->getResultCount();
            $this->setResultCount($result_count++);
            $this->setSuccess(true);
        }
        if($addNotification){
            $this->setNotification();
        }
        return $this;
    }
}