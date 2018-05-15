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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use MesClics\PostBundle\Form\MesClicsCollectionType;
use MesClics\PostBundle\Repository\CollectionRepository;
use MesClics\PostBundle\Entity\Collection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
        ->add('collections_select', EntityType::class, array(
            'class' => 'MesClicsPostBundle:Collection',
            'query_builder' => function(CollectionRepository $repo){
                return $repo->getForQB('post');
            },
            'property_path' => 'collections',
            'choice_label' => function(Collection $collection){
                return $collection->getFormLabel();
            },
            'choice_attr' => function(Collection $collection, $key, $index){
                return ['class' => '[ oocss-form-input-button ]',
                        'title' => $collection->getDescription()];
            },
            'expanded' => true,
            'multiple' => true,
            'required' => false
        ))
        ->add('collections_add', CollectionType::class, array(
            'label' => 'associer à une collection',
            'property_path' => 'collections',
            'entry_type' => MesClicsCollectionType::class,
            'allow_add' => true,
            'allow_delete' => false,
            'required' => false
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Ajouter'
        ));

        // $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
        //     //on récupère tous les nouveaux champs collection
        //     if(isset($event->getData()['collections_select'])){
        //         $collections = $event->getData()['collections_select'];
        //         foreach($collections as $collection){
        //             // var_dump($collection);
        //         }
        //     }
        //     if(isset($event->getData()['collections_add'])){
        //         $collections_add = $event->getData()['collections_add'];
        //         foreach($collections_add as $collection){
        //             // var_dump($collection);
        //         }
        //     }
        // });

        // $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
        //     $data = $event->getData();
        //     $collections = $data->getCollections();
        //     foreach($collections as $collection){
        //         var_dump($collection->getName());
        //     }
        //     die();
        // });
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
