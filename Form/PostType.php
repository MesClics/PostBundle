<?php

namespace MesClics\PostBundle\Form;

use Symfony\Component\Form\FormEvent;
use MesClics\PostBundle\Form\DTO\Test;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use MesClics\PostBundle\Form\DTO\PostDTO;
use MesClics\PostBundle\Entity\Collection;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MesClics\PostBundle\Repository\CollectionRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use MesClics\PostBundle\Form\MesClicsPostsCollectionEmbedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, array(
            'required' => false
        ))
        ->add('content', CKEditorType::class, array(
            'required' => false
        ))
        ->add('date_publication', DateTimeType::class, array(
            'required' => false
        ))
        ->add('date_peremption', DateTimeType::class, array(
            'required' => false
        ))
        ->add('visibilite', ChoiceType::class, array(
            'expanded' => true,
            'choices' => array(
                'public' => 'public',
                'privé' => 'private'
            )
        ))
        // TODO: modify to use DTO not Entity
        ->add('collections_select', EntityType::class, array(
            'class' => 'MesClicsPostBundle:Collection',
            'query_builder' => function(CollectionRepository $repo){
                return $repo->getCollectionsQB('post');
            },
            'choice_label' => function(Collection $collection){
                return $collection->getFormLabel();
            },
            'choice_attr' => function(Collection $collection, $key, $index){
                return ['class' => 'oocss-form-input-button',
                        'title' => $collection->getDescription()];
            },
            'expanded' => true,
            'multiple' => true,
            'required' => false
        ))
        ->add('newcollections', CollectionType::class, array(
            'entry_type' => MesClicsPostsCollectionEmbedType::class,
            'allow_add' => true,
            'allow_delete' => false,
            'prototype' => true,            
        ))
        ->add('submit', SubmitType::class);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PostDTO::class
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
