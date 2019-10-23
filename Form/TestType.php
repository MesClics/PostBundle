<?php

namespace MesClics\PostBundle\Form;

use MesClics\PostBundle\Form\DTO\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TestType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
        ->add('name', TextType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            // 'data_class' => 'MesClics\PostBundle\Entity\Post',
            'data_class' => Test::class
        ));
    }
}