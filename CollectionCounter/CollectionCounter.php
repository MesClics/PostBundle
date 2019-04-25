<?php
namespace MesClics\PostBundle\CollectionCounter;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CollectionCounter{
    private $em;
    private $token_storage;
    private $counter_types;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token_storage){
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->counter_types = array(
            'post', // les colelctions de publications
            'user', // les collections d'utilisateurs
            'client', // les collections de clients
            'message', // les collections de messages
            'media', //les collections de medias
        );
    }

    public function count($counter_type = null){
        $repo = $this->em->getRepository('MesClicsPostBundle:Collection');
        //si on n'indique pas le type de compteur, alors on retourne tous les compteurs
        // if(!$counter_type){
        //     $counters = array();
        //     foreach($this->counter_types as $counter){
        //         $method_name = 'countCollections(' . $counter . ')';
        //         $counters[$counter] = $repo->$method_name();
        //     }
        //     return $counters;
        // }
        //sinon on vérifie que le type de compteur est valide
        if($counter_type){
            if(!in_array($counter_type, $this->counter_types)){
                throw new InvalidArgumentException('Le type de compteur ne peut être que l\'un des suivants : ' . implode(', ', $this->counter_types));
            }
            $method_name = 'countCollections(' . $counter_type . ')';
        } else{
            $method_name = 'countCollections';
        }
        
        return $repo->$method_name();
    }

}