<?php

namespace MesClics\PostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use MesClics\PostBundle\Form\CollectionType;
use MesClics\PostBundle\Repository\CollectionRepository;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, array(
            'label' => 'Titre de la publication',
            'required' => false
        ))
        ->add('content', CKEditorType::class, array(
            'label' => 'Rédigez votre publication',
            'required' => false
        ))
        ->add('date_publication', DateTimeType::class, array(
            'label' => 'Date de mise en ligne',
            'required' => false
        ))
        ->add('date_peremption', DateTimeType::class, array(
            'label' => 'Date de fin de mise en ligne',
            'required' => false
        ))
        ->add('visibilite', ChoiceType::class, array(
            'label' => 'Visibilité de la publication',
            'expanded' => true,
            'choices' => array(
                'public' => 'public',
                'privé' => 'private'
            ),
            'empty_data' => 'private'
        ))
        ->add('collections', EntityType::class, array(
            'class' => 'MesClicsPostBundle:Collection',
            'query_builder' => function(CollectionRepository $repo){
                return $repo->getForQB('post');
            },
            'choice_label' => 'name',
            'label' => 'Ajouter aux collections',
            'expanded' => true,
            'multiple' => true,
            'required' => false
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Ajouter'
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MesClics\PostBundle\Entity\Post'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_postbundle_post';
    }


}
