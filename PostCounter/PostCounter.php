<?php
namespace MesClics\PostBundle\PostCounter;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostCounter{
    private $em;
    private $token_storage;
    private $counter_types;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token_storage){
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->counter_types = array(
            'all',
            'published', //articles ayant été publiés à un moment où un autre
            'toBePublished', //article dont la publication est prévue
            'unpublished', //articles ayant déjà été dépubliés,
            'toBeUnpublished', //articles qui ont une date de dépublication prévue
            'online', //articles actuellement en ligne
            'drafts' //articles n'ayant jamais eu de date de publication
        );
    }

    public function count($counter_type = false){
        $post_repo = $this->em->getRepository('MesClicsPostBundle:Post');
        //si on n'indique pas le type de compteur, alors on retourne tous les compteurs
        if(!$counter_type){
            $counters = array();
            foreach($this->counter_types as $counter){
                $method_name = 'count' . ucfirst($counter) . 'Posts';
                $counters[$counter] = $post_repo->$method_name($this->token_storage->getToken()->getUser());
            }
            return $counters;
        }
        //sinon on vérifie que le type de compteur est valide
        if(!in_array($counter_type, $this->counter_types)){
            throw new InvalidArgumentException('Le type de compteur ne peut être que l\'un des suivants : ' . implode(', ', $this->counter_types));
        }
        $method_name = 'count' . ucfirst($counter_type) . 'Posts';
        return $post_repo->$method_name($this->token_storage->getToken()->getUser());
    }
}