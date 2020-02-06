<?php

namespace MesClics\PostBundle\Form;

use Symfony\Component\Form\AbstractType;
use MesClics\PostBundle\Form\DTO\CollectionDTO;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MesClicsCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $available_collections = $options['available_collections'];
        $builder
        ->add('name', TextType::class)
        ->add('entity', ChoiceType::class, array(
            'expanded' => false,
            'multiple' => false,
            'choices' => $available_collections
        ))
        ->add('description', TextType::class)
        ->add('submit', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CollectionDTO::class,
            'available_collections' => null
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
