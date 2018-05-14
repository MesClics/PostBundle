<?php

namespace MesClics\PostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, array(
            'label' => 'nom de la collection'
        ))
        ->add('entity', ChoiceType::class, array(
            'label' => 'type d\'objet',
            'expanded' => false,
            'multiple' => false,
            'choices' => array(
                'Publication' => 'post',
                'Utilisateur' => 'user',
                'Message' => 'message',
                'Client' => 'client',
                'Collection' => 'collection'
            )
        ))
        ->add('description', TextType::class, array(
            'label' => 'description de la collection'
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'ajouter'
        ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MesClics\PostBundle\Entity\Collection'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_postbundle_collection';
    }


}
