<?php
namespace MesClics\PostBundle\Twig;

use MesClics\PostBundle\PostCounter\PostCounter;
use MesClics\PostBundle\CollectionCounter\CollectionCounter;

class PostCounterExtension extends \Twig_Extension{
    private $post_counter;
    private $collection_counter;

    public function __construct(PostCounter $post_counter, CollectionCounter $collection_counter){
        $this->post_counter = $post_counter;
        $this->collection_counter = $collection_counter;
    }

    public function getPostsCount($counter_type = false){
        return $this->post_counter->count($counter_type);
    }

    public function getCollectionsCount($counter_type = null){
        return $this->collection_counter->count($counter_type);
    }

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('countPosts', array($this, 'getPostsCount')),
            new \Twig_SimpleFunction('countCollections', array($this, 'getCollectionsCount'))
        );
    }

    public function getName(){
        return 'MesClicsPostCounter';
    }
}