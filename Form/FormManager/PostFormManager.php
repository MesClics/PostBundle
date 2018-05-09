<?php
namespace MesClics\PostBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;

class PostFormManager extends FormManager{
    const ERROR_NOTIFICATION_SINGULIER = "La publication n'a pas pu être créée. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "La publication a bien été créée.";
}