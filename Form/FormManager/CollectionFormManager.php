<?php
namespace MesClics\PostBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;

class CollectionFormManager extends FormManager{
    const ERROR_NOTIFICATION_SINGULIER = "La collection n'a pas pu être créée. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "La collection a bien été créée.";
}