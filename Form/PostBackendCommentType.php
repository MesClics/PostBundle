<?php
namespace MesClics\PostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MesClics\PostBundle\Form\DTO\PostBackendCommentDTO;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostBackendCommentType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
        ->add("content", TextareaType::class)
        ->add("submit", SubmitType::class);
    }

    public function configureOprions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            "data_class" => PostBackendCommentDTO::class
        ));
    }

    public function getBlockPrefix(){
        return 'mesclics_postbundle_post_backend_comment';
    }
}